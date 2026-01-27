<?php

namespace App\Modules\Order\Actions;

use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Modules\Product\Models\Product;
use App\Modules\Order\DTOs\OrderItemDTO;
use App\Modules\Order\Enums\OrderStatus;
use function Symfony\Component\Clock\now;
use App\Modules\Order\DTOs\CreateOrderDTO;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    /**
     * Create order with product and discount data
     */
    public function execute(CreateOrderDTO $dto): Order
    {
        /** @var \App\Modules\Auth\Models\User $user */
        $user = Auth::user();

        $item = $dto->item;

        $product = Product::where('code', $item->code)->firstOrFail();

        if ($item->normalPrice != $product->price) {
            throw ValidationException::withMessages(['message' => ['Normal price is invalid.']]);
        }

        if ($item->totalPrice != ($item->qty * $product->price)) {
            throw ValidationException::withMessages(['message' => ['Total price is invalid.']]);
        }

        if (is_null($item->discountPrice) == $product->is_discount) {
            throw ValidationException::withMessages(['message' => ['Discount is invalid.']]);
        }

        if ($item->discountPrice && $product->is_discount) {
            $this->validateProductDiscountPrice($item, $product);
        }

        if ($item->finalPrice !== ($item->totalPrice - (float) $item->discountPrice)) {
            throw ValidationException::withMessages(['message' => ['Final price is invalid.']]);
        }

        $this->validateOrderPrice($dto);

        DB::beginTransaction();
        try {
            $order = $user->orders()->create(array_merge($dto->toArray(), [
                'status' => OrderStatus::PENDING,
                'transaction_date' => now(),
            ]));

            $order->orderItem()->create(array_merge($item->toArray(), [
                'product_id' => $product->id,
                'discount_id' => $product->activeDiscount?->id,
                'product_snapshot' => $product->snapshot(),
            ]));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $order;
    }

    /**
     * Validate discount price
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
     */
    protected function validateOrderPrice(CreateOrderDTO $dto): void
    {
        $taxAmount = Order::getTaxAmount($dto->subTotal);

        if ($dto->grandTotal !== ($dto->subTotal + $taxAmount)) {
            throw ValidationException::withMessages([
                'message' => ['Price calculation mismatch. Check subtotal, tax, and grand total.'],
            ]);
        }
    }
}
