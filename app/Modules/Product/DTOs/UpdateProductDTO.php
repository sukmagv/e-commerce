<?php

namespace App\Modules\Product\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Modules\Product\Enums\DiscountType;

class UpdateProductDTO extends BaseDTO
{
    public ?int $category_id;
    public ?string $name;
    public ?UploadedFile $photo;
    public ?float $price;
    public ?bool $is_discount;
    public ?bool $is_active;
    public ?DiscountDTO $discount;
}
