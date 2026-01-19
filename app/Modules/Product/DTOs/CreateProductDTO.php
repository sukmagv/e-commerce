<?php

namespace App\Modules\Product\DTOs;

use App\Modules\Product\Enums\DiscountType;
use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CreateProductDTO extends BaseDTO
{
    public int $category_id;
    public string $name;
    public UploadedFile $photo;
    public int $price;
    public bool $is_discount;
    public ?DiscountType $type;
    public ?int $amount;
    public ?int $final_price;
}
