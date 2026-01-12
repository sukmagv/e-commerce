<?php

namespace App\Http\Requests;

use App\Rules\VerifiedOtp;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends BaseRequest
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
}
