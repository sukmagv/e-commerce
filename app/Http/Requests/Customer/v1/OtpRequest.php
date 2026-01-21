<?php

namespace App\Http\Requests\Customer\v1;

use Illuminate\Validation\Rule;
use App\Modules\Auth\Enums\OtpType;
use Illuminate\Foundation\Http\FormRequest;

class OtpRequest extends FormRequest
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
            'address' => ['required', 'email', 'unique:otps,address'],
            'type' => ['required', Rule::enum(OtpType::class)],
        ];
    }
}
