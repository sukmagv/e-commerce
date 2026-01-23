<?php

namespace App\Modules\Order\DTOs;

use App\Supports\BaseDTO;

class CreateOrderDTO extends BaseDTO
{
    public function __construct(
        public float $subTotal,
        public float $taxAmount,
        public float $grandTotal,
        public ?string $note,
        public OrderItemDTO $item,
    ) {}
}
