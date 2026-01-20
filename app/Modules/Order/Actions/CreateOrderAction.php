<?php

namespace App\Modules\Order\Actions;

use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use App\Supports\DiscountValidation;
use Illuminate\Support\Facades\Auth;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Product\Models\Product;
use App\Modules\Order\Enums\OrderStatus;
use function Symfony\Component\Clock\now;
use App\Modules\Order\DTOs\CreateOrderDTO;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    public function execute(CreateOrderDTO $dto): Order
    {
        DB::beginTransaction();
        try {
            /** @var \App\Modules\Auth\Models\User $user */
            $user = Auth::user();

            $order = Order::create(array_merge(
                $dto->toArray(),
                [
                    'user_id' => $user->id,
                    'status' => OrderStatus::PENDING,
                    'transaction_date' => now(),
                ]
            ));

            $product = Product::where('code', $dto->item->code)->firstOrFail();

            $this->validateProductPrice($dto, $product);

            $orderItem = new OrderItem(array_merge(
                $dto->item->toArray(),
                [
                    'product_id' => $product->id,
                    'discount_id' => $product->activeDiscount->id
                ]
            ));

            $this->validateOrderPrice($dto);

            $order->update($dto->toArray());

            $orderItem->order()->associate($order);

            $orderItem->save();

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $order;
    }

    /**
     * Validate all product and discount price
     *
     * @param \App\Modules\Order\DTOs\CreateOrderDTO $dto
     * @param \App\Modules\Product\Models\Product $product
     * @return void
     */
    protected function validateProductPrice(CreateOrderDTO $dto, Product $product): void
    {
        if ($dto->item->normal_price != $product->price) {
            throw ValidationException::withMessages(['message' => ['Normal price is invalid.']]);
        }

        if ($dto->item->total_price != ($dto->item->qty * $product->price)) {
            throw ValidationException::withMessages(['message' => ['Total price is invalid.']]);
        }

        if ($product->is_discount) {
            if (
                $dto->item->discount->type !== $product->activeDiscount->type ||
                $dto->item->discount->amount != $product->activeDiscount->amount
            ) {
                throw ValidationException::withMessages(['message' => ['Discount is invalid.']]);
            }

            DiscountValidation::calculateFinalPrice(
                $dto->item->normal_price,
                $dto->item->discount->type,
                $dto->item->discount->amount,
                $dto->item->discount->final_price
            );
        }

        $expectedDiscountPrice = $dto->item->qty * ($dto->item->normal_price - $dto->item->discount->final_price);
        if ($dto->item->discount_price != $expectedDiscountPrice) {
            throw ValidationException::withMessages(['message' => ['Discount price is invalid.']]);
        }

        if ($dto->item->final_price !== ($dto->item->total_price - $dto->item->discount_price)) {
            throw ValidationException::withMessages(['message' => ['Final price is invalid.']]);
        }
    }

    /**
     * Validate sub total, tax amount and grand total order
     *
     * @param \App\Modules\Order\DTOs\CreateOrderDTO $dto
     * @return void
     */
    protected function validateOrderPrice(CreateOrderDTO $dto): void
    {
        $taxAmount = ($dto->item->final_price * 11) / 100; // bikin const di model

        if ($dto->sub_total != $dto->item->final_price) {
            throw ValidationException::withMessages(['message' => ['Sub total price is invalid.']]);
        }

        if ($dto->tax_amount != $taxAmount) {
            throw ValidationException::withMessages(['message' => ['Tax amount is invalid.' . $taxAmount]]);
        }

        if ($dto->grand_total != $dto->sub_total + $taxAmount) {
            throw ValidationException::withMessages(['message' => ['Grand total price is invalid.']]);
        }
    }
}
