<?php

namespace App\Http\Requests\Api\Customer\V1;

use App\Modules\Auth\DTOs\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function payload(): LoginDTO
    {
        return LoginDTO::fromArray($this->validated());
    }
}
