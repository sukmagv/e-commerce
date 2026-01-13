<?php

namespace App\Http\Requests\Admin\v1;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;
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
            'name'        => ['required', 'string'],
            'photo'       => ['required', 'file', 'max:2048'],
            'price'       => ['required', 'numeric', 'min:0'],
            'is_discount' => ['required', 'boolean'],
            'type'        => ['required_if:is_discount,true,1', 'string', Rule::enum(DiscountType::class)],
            'amount'      => ['required_if:is_discount,true,1', 'numeric', 'min:0'],
            'final_price' => ['required_if:is_discount,true,1', 'numeric', 'min:0'],
        ];
    }
}
