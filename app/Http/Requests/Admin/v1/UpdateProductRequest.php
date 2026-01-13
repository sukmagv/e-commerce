<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Validation\Rule;
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
            'name'        => ['sometimes', 'string'],
            'photo'       => ['sometimes', 'file', 'max:2048'],
            'price'       => ['sometimes', 'integer'],
            'is_discount' => ['sometimes', 'boolean'],
            'type'        => ['sometimes', 'string', Rule::enum(DiscountType::class)],
            'amount'      => ['sometimes', 'numeric', 'min:0'],
            'final_price' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
