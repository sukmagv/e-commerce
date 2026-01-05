<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp_id' => 'required|exists:otps,id',
            'code' => 'nullable|string',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:11',
            'photo' => 'required|file',
            'password' => 'required|string|min:6',
            'is_blocked' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'otp_id.required' => 'OTP ID is required',
            'otp_id.exists' => 'OTP ID is not valid',
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Format email is required',
            'phone.required' => 'Phone is required',
            'photo.required' => 'Photo is required',
            'password.required' => 'Password is required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
