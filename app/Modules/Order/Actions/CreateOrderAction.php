<?php

namespace App\Modules\Order\Actions;

use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
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

        if ($item->normalPrice != $product->price || $item->totalPrice != ($item->qty * $product->price)) {
            throw ValidationException::withMessages(['message' => ['Invalid normal or total price.']]);
        }

        if ((bool) $item->discountPrice !== $product->is_discount) {
            throw ValidationException::withMessages(['message' => ['Discount is invalid.']]);
        }

        if ($item->discountPrice && $product->is_discount){
            $this->validateProductDiscountPrice($item, $product);
        }

        if ($item->finalPrice !== ($item->totalPrice - (float)$item->discountPrice)) {
            throw ValidationException::withMessages(['message' => ['Final price is invalid.']]);
        }

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
     * Validate discount price
     *
     * @param \App\Modules\Order\DTOs\OrderItemDTO $item
     * @param \App\Modules\Product\Models\Product $product
     * @return void
     */
    protected function validateProductDiscountPrice(OrderItemDTO $item, Product $product): void
    {
        $expectedDiscountPrice = $item->qty * ($item->normalPrice - $product->activeDiscount->final_price);

        if ($item->discountPrice !== $expectedDiscountPrice) {
            throw ValidationException::withMessages(['message' => ['Discount price is invalid.']]);
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
        $taxAmount = Order::getTaxAmount($dto->subTotal);

        $isInvalid = ($dto->subTotal !== $dto->item->finalPrice) ||
                    ($dto->taxAmount !== $taxAmount) ||
                    ($dto->grandTotal !== ($dto->subTotal + $taxAmount));

        if ($isInvalid) {
            throw ValidationException::withMessages([
                'message' => ['Price calculation mismatch. Check subtotal, tax, and grand total.']
            ]);
        }
    }
}
