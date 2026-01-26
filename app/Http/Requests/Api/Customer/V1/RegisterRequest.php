<?php

namespace App\Http\Requests\Api\Customer\V1;

use App\Rules\V1\VerifiedOtp;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'min:11', 'regex:/^[0-9]+$/'],
            'photo' => ['nullable', 'file', 'max:2048'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function payload(): CustomerRegisterDTO
    {
        return CustomerRegisterDTO::fromArray($this->validated());
    }
}
