<?php

namespace App\Http\Requests\Customer\v1;

use Illuminate\Validation\Rule;
use App\Supports\BaseRequest;
use App\Modules\Product\Enums\DiscountType;

class CreateOrderRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item' => ['required', 'array', 'min:1'],
            'item.code' => ['required', 'string', 'exists:products,code'],
            'item.qty' => ['required', 'integer', 'min:1'],
            'item.normal_price' => ['required', 'numeric', 'min:1'],
            'item.total_price' => ['required', 'numeric', 'min:1'],
            'item.discount_price' => ['required', 'numeric', 'min:1'],

            'item.discount' => ['sometimes','array',],
            'item.discount.type' => ['required', 'string', Rule::enum(DiscountType::class)],
            'item.discount.amount' => ['required', 'numeric', 'min:1'],
            'item.discount.final_price' => ['required', 'numeric', 'min:1'],

            'item.final_price' => ['required', 'numeric', 'min:1'],

            'sub_total' => ['required', 'numeric', 'min:1'],
            'tax_amount' => ['required', 'numeric', 'min:1'],
            'grand_total' => ['required', 'numeric', 'min:1'],
            'note' => ['nullable', 'string', 'max:100'],
        ];
    }
}
