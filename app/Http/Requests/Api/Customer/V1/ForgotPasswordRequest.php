<?php

namespace App\Http\Requests\Api\Customer\V1;

use App\Modules\Auth\DTOs\ForgotPasswordDTO;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp_id' => ['nullable', 'integer'],
            'address' => ['required', 'email', 'exists:users,email'],
        ];
    }

    public function payload(): ForgotPasswordDTO
    {
        return ForgotPasswordDTO::fromArray($this->validated());
    }
}
