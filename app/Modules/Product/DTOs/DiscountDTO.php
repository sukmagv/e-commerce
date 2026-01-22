<?php

namespace App\Modules\Product\DTOs;

use App\Modules\Product\Enums\DiscountType;
use App\Supports\BaseDTO;

class DiscountDTO extends BaseDTO
{
    public function __construct(
        public DiscountType $type,
        public float $amount,
        public float $finalPrice,
    ) {}
}
