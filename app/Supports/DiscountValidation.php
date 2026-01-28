<?php

namespace App\Supports;

use App\Modules\Product\DTOs\DiscountDTO;
use App\Modules\Product\Enums\DiscountType;
use App\Modules\Product\Models\Product;
use Illuminate\Validation\ValidationException;

class DiscountValidation
{
    /**
     * Validate input price with calculated price from database
     *
     * @param float $price
     * @param \App\Modules\Product\DTOs\DiscountDTO $discount
     * @return float
     */
    public static function calculateFinalPrice(float $price, DiscountDTO $discount): float
    {
        $expectedFinalPrice = match ($discount->type) {
            DiscountType::NOMINAL =>
                $price - $discount->amount,

            DiscountType::PERCENTAGE =>
                round($price - ($price * $discount->amount / 100)),
        };

        if ($expectedFinalPrice !== $discount->finalPrice) {
            throw ValidationException::withMessages([
                'message' => ['Discount Final price is invalid.'],
            ]);
        }

        return (float) round($expectedFinalPrice);
    }
}
