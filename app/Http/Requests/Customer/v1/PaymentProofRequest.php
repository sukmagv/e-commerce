<?php

namespace App\Http\Requests\Customer\v1;

use Illuminate\Validation\Rule;
use App\Modules\Order\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;

class PaymentProofRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proof_link' => ['required', 'file', 'max:2048'],
            'type'       => ['required', 'string', Rule::enum(PaymentType::class)],
            'note'       => ['nullable', 'string', 'max:100'],
        ];
    }
}
