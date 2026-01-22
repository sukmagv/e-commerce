<?php

namespace App\Modules\Product\DTOs;

use App\Modules\Product\Enums\DiscountType;
use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CreateProductDTO extends BaseDTO
{
    public function __construct(
        public int $categoryId,
        public string $name,
        public UploadedFile $photo,
        public float $price,
        public bool $isDiscount,
        public ?DiscountDTO $discount,
    ) {}
}
