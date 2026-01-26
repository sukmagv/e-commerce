<?php

namespace App\Http\Requests\Api\Customer\V1;

use App\Modules\Order\DTOs\UploadPaymentProofDTO;
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

    public function payload(): UploadPaymentProofDTO
    {
        return UploadPaymentProofDTO::fromArray($this->validated());
    }
}
