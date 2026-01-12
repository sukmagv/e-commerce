<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'min:11', 'regex:/^[0-9]+$/'],
            'photo' => ['sometimes', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
