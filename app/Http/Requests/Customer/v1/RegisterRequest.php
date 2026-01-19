<?php

namespace App\Http\Requests\Customer\v1;

use App\Rules\VerifiedOtp;
use App\Supports\BaseRequest;

class RegisterRequest extends BaseRequest
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
}
