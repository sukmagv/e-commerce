<?php

namespace App\Modules\Product\DTOs;

use App\Modules\Product\Enums\DiscountType;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CreateProductDTO
{
    public int $category_id;
    public string $name;
    public UploadedFile $photo;
    public int $price;
    public bool $is_discount;
    public ?DiscountType $type;
    public ?int $amount;
    public ?int $final_price;

    /**
     * Create DTO instance
     * Maps input data from the request (form-data or JSON)
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->category_id = $request->input('category_id');
        $dto->name = $request->input('name');
        $dto->photo = $request->file('photo');
        $dto->price = $request->input('price');
        $dto->is_discount = $request->boolean('is_discount');
        $dto->amount = $request->input('amount');
        $dto->final_price = $request->input('final_price');

        $inputType = $request->input('type');
        $dto->type = ($dto->is_discount && $inputType) ? DiscountType::from($inputType) : null;

        return $dto;
    }
}
