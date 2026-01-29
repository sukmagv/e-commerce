<?php

namespace App\Modules\Order\DTOs;

use App\Modules\Product\DTOs\DiscountDTO;
use App\Supports\BaseDTO;

class OrderItemDTO extends BaseDTO
{
    public function __construct(
        public string $code,
        public int $qty,
        public float $normalPrice,
        public float $totalPrice,
        public ?float $discountPrice,
        public ?DiscountDTO $discount,
        public float $finalPrice,
    ) {}
}
