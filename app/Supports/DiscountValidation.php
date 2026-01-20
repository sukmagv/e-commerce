<?php

namespace App\Supports;

use App\Modules\Product\Enums\DiscountType;
use Illuminate\Validation\ValidationException;

class DiscountValidation
{
    /**
     * Validate input price with calculated price from database
     *
     * @param float $price
     * @param \App\Modules\Product\Enums\DiscountType $type
     * @param float $amount
     * @param float $finalPrice
     * @return integer
     */
    public static function calculateFinalPrice(float $price, DiscountType $type, float $amount, float $finalPrice): int
    {
        $expectedFinalPrice = match ($type) {
            DiscountType::NOMINAL =>
                $price - $amount,

            DiscountType::PERCENTAGE =>
                round($price - ($price * $amount / 100)),
        };

        if ($expectedFinalPrice !== $finalPrice) {
            throw ValidationException::withMessages([
                'message' => ['Discount Final price is invalid.'],
            ]);
        }

        return (float) round($expectedFinalPrice);
    }
}
