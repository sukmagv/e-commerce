<?php

namespace App\Http\Requests\Api\Customer\V1;

use App\Rules\V1\VerifiedOtp;
use App\Modules\Auth\DTOs\ResetPasswordDTO;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp_id' => ['required', 'exists:otps,id', new VerifiedOtp],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function payload(): ResetPasswordDTO
    {
        return ResetPasswordDTO::fromArray($this->validated());
    }
}
