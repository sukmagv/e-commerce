<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Validation\Rule;
use App\Rules\DiscountValidation;
use App\Supports\BaseRequest;
use App\Modules\Product\Enums\DiscountType;

class UpdateProductRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:product_categories,id'],
            'name'        => ['sometimes', 'string', 'max:100'],
            'photo'       => ['sometimes', 'file', 'max:2048'],
            'price'       => ['required_with:discount', 'integer'],
            'is_discount' => ['required_with:discount,price', 'boolean'],
            'is_active  ' => ['sometimes', 'boolean'],

            'discount'             => ['required_if:is_discount,true,1','array'],
            'discount.type'        => ['required_if:is_discount,true,1', 'string', Rule::enum(DiscountType::class)],
            'discount.amount'      => ['required_if:is_discount,true,1', 'numeric', 'min:1'],
            'discount.final_price' => ['required_if:is_discount,true,1', 'numeric', 'min:1'],
        ];
    }
}
