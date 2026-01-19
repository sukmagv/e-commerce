<?php

namespace App\Http\Requests\Customer\v1;

use App\Supports\BaseRequest;

class ForgotPasswordRequest extends BaseRequest
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
}
