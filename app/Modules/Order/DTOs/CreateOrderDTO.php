<?php

namespace App\Modules\Order\DTOs;

use App\Modules\Product\Enums\DiscountType;
use App\Supports\BaseDTO;
use Illuminate\Http\Request;

class CreateOrderDTO extends BaseDTO
{
    public float $finalPrice;
    public float $subTotal;
    public float $taxAmount;
    public float $grandTotal;
    public ?string $note;
    public OrderItemDTO $item;
}
