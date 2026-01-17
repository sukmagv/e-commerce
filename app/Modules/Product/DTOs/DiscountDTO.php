<?php

namespace App\Modules\Product\DTOs;

use App\Modules\Product\Enums\DiscountType;
use App\Supports\BaseDTO;

class DiscountDTO extends BaseDTO
{
    public DiscountType $type;
    public float $amount;
    public float $final_price;
}
