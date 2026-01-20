<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Validation\Rule;
use App\Supports\BaseRequest;
use App\Modules\Product\Enums\DiscountType;

class CreateProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:product_categories,id'],
            'name'        => ['required', 'string', 'max:100'],
            'photo'       => ['required', 'file', 'max:2048'],
            'price'       => ['required', 'numeric', 'min:1'],
            'is_discount' => ['required', 'boolean'],

            'discount'             => ['required_if:is_discount,true,1','array'],
            'discount.type'        => ['required_with:discount', 'string', Rule::enum(DiscountType::class)],
            'discount.amount'      => ['required_with:discount', 'numeric', 'min:1'],
            'discount.final_price' => ['required_with:discount', 'numeric', 'min:1'],
        ];
    }
}
