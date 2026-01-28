<?php

namespace App\Http\Requests\Api\Customer\V1;

use App\Modules\Order\DTOs\CreateOrderDTO;
use Illuminate\Validation\Rule;
use App\Modules\Product\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item'                => ['required', 'array', 'min:1'],
            'item.code'           => ['required', 'string', 'exists:products,code'],
            'item.qty'            => ['required', 'integer', 'min:1'],
            'item.normal_price'   => ['required', 'numeric', 'min:1'],
            'item.total_price'    => ['required', 'numeric', 'min:1'],
            'item.discount_price' => ['nullable', 'numeric', 'min:1'],

            'item.discount'             => ['required_with:item.discount_price', 'array'],
            'item.discount.type'        => ['required_with:item.discount.final_price', 'string', Rule::enum(DiscountType::class)],
            'item.discount.amount'      => ['required_with:item.discount.final_price', 'numeric', 'min:1'],
            'item.discount.final_price' => ['required_with:item.discount.type', 'numeric', 'min:1'],

            'item.final_price' => ['required', 'numeric', 'min:1'],

            'sub_total'   => ['required', 'numeric', 'min:1'],
            'tax_amount'  => ['required', 'numeric', 'min:1'],
            'grand_total' => ['required', 'numeric', 'min:1'],
            'note'        => ['nullable', 'string', 'max:100'],
        ];
    }

    public function payload(): CreateOrderDTO
    {
        return CreateOrderDTO::fromArray($this->validated());
    }
}
