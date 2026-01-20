<?php

namespace App\Modules\Order\DTOs;

use App\Modules\Product\Enums\DiscountType;
use App\Supports\BaseDTO;
use Illuminate\Http\Request;

class CreateOrderDTO extends BaseDTO
{
    public float $final_price;
    public float $sub_total;
    public float $tax_amount;
    public float $grand_total;
    public ?string $note;
    public OrderItemDTO $item;
}
