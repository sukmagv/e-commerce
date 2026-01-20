<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Validation\Rule;
use App\Rules\DiscountValidation;
use App\Http\Requests\BaseRequest;
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
            'is_discount' => ['required_with:discount', 'boolean'],

            'discount' => [
                'sometimes',
                'array',
                new DiscountValidation(
                    $this->input('price'),
                    $this->boolean('is_discount')
                ),
            ],
            'discount.type'        => ['required_with_all:discount.amount,discount.final_price', 'string', Rule::enum(DiscountType::class)],
            'discount.amount'      => ['required_with_all:discount.type,discount.final_price', 'numeric', 'min:1'],
            'discount.final_price' => ['required_with_all:discount.type,discount.amount', 'numeric', 'min:1'],
        ];
    }
}
