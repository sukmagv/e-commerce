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
use App\Modules\Order\DTOs\OrderItemDTO;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    /**
     * Create order with product and discount data
     *
     * @param \App\Modules\Order\DTOs\CreateOrderDTO $dto
     * @return \App\Modules\Order\Models\Order
     */
    public function execute(CreateOrderDTO $dto): Order
    {
        /** @var \App\Modules\Auth\Models\User $user */
        $user = Auth::user();

        $item = $dto->item;

        $product = Product::where('code', $item->code)->firstOrFail();

        if (!$product->activeDiscount) {
            throw ValidationException::withMessages(['message' => ['Discount price is invalid. Product doesn\'t have discount']]);
        }

        $this->validateProductPrice($item, $product);

        $this->validateOrderPrice($dto);

        DB::beginTransaction();
        try {
            $order = Order::create(array_merge(
                $dto->toArray(),
                [
                    'user_id' => $user->id,
                    'status' => OrderStatus::PENDING,
                    'transaction_date' => now(),
                ]
            ));

            $orderItem = new OrderItem(array_merge(
                $item->toArray(),
                [
                    'product_id' => $product->id,
                    'discount_id' => $product->activeDiscount?->id
                ]
            ));

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
     * @param \App\Modules\Order\DTOs\OrderItemDTO $item
     * @param \App\Modules\Product\Models\Product $product
     * @return void
     */
    protected function validateProductPrice(OrderItemDTO $item, Product $product): void
    {
        if ($item->normalPrice != $product->price) {
            throw ValidationException::withMessages(['message' => ['Normal price is invalid.']]);
        }

        if ($item->totalPrice != ($item->qty * $product->price)) {
            throw ValidationException::withMessages(['message' => ['Total price is invalid.']]);
        }

        if ($product->is_discount) {
            if (
                $item->discount->type !== $product->activeDiscount->type ||
                $item->discount->amount != $product->activeDiscount->amount
            ) {
                throw ValidationException::withMessages(['message' => ['Discount is invalid.']]);
            }

            DiscountValidation::calculateFinalPrice($product, $item->discount);
        }

        $expectedDiscountPrice = $item->qty * ($item->normalPrice - $item->discount->finalPrice);
        if ($item->discountPrice != $expectedDiscountPrice) {
            throw ValidationException::withMessages(['message' => ['Discount price is invalid.']]);
        }

        if ($item->finalPrice !== ($item->totalPrice - $item->discountPrice)) {
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
        $taxAmount = ($dto->item->finalPrice * Order::TAX) / 100;

        if ($dto->subTotal != $dto->item->finalPrice) {
            throw ValidationException::withMessages(['message' => ['Sub total price is invalid.']]);
        }

        if ($dto->taxAmount != $taxAmount) {
            throw ValidationException::withMessages(['message' => ['Tax amount is invalid.' . $taxAmount]]);
        }

        if ($dto->grandTotal != $dto->subTotal + $taxAmount) {
            throw ValidationException::withMessages(['message' => ['Grand total price is invalid.']]);
        }
    }
}
