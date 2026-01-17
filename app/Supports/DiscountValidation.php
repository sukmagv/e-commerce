<?php

namespace App\Supports;

use InvalidArgumentException;
use App\Modules\Product\Enums\DiscountType;
use Illuminate\Validation\ValidationException;

class DiscountValidation
{
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
