<?php

namespace App\Modules\Product\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Modules\Product\Enums\DiscountType;

class UpdateProductDTO extends BaseDTO
{
    public function __construct(
        public ?int $categoryId,
        public ?string $name,
        public ?UploadedFile $photo,
        public ?float $price,
        public ?bool $isDiscount,
        public ?bool $isActive,
        public ?DiscountDTO $discount,
    ) {}
}
