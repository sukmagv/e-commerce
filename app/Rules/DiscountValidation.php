<?php

namespace App\Rules;

use App\Modules\Product\Enums\DiscountType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DiscountValidation implements ValidationRule
{
    protected int $price;
    protected bool $isDiscount;

    public function __construct(int $price, bool $isDiscount)
    {
        $this->price = $price;
        $this->isDiscount = $isDiscount;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->isDiscount) {
            return;
        }

        $amount = $value['amount'] ?? 0;
        $type = $value['type'] ?? null;
        $finalPrice = $value['final_price'] ?? null;

        if (!$type) {
            return;
        }

        $expectedFinalPrice = match (DiscountType::from($type)) {
            DiscountType::NOMINAL =>
                $this->price - $amount,

            DiscountType::PERCENTAGE =>
                $this->price - ($this->price * $amount / 100),
        };

        // harga sesudah diskon tidak dibawah 0
        if ($expectedFinalPrice < 0) {
            $fail('The discount amount exceeds the product price.', null);
            return;
        }

        if (round($expectedFinalPrice) !== round($finalPrice)) {
            $fail('The final price calculation is not valid. Expected: ' . $expectedFinalPrice, null);
        }
    }
}
