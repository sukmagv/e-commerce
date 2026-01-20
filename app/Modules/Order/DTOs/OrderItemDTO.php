<?php

namespace App\Modules\Order\DTOs;

use App\Modules\Product\DTOs\DiscountDTO;
use App\Supports\BaseDTO;

class OrderItemDTO extends BaseDTO
{
    public string $code;
    public int $qty;
    public float $normal_price;
    public float $total_price;
    public float $discount_price;
    public ?DiscountDTO $discount;
    public float $final_price;
}
