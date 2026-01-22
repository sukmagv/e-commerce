<?php

namespace App\Http\Requests\Customer\v1;

use App\Modules\Auth\DTOs\ChangePasswordDTO;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'min:6'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function payload(): ChangePasswordDTO
    {
        return ChangePasswordDTO::fromArray($this->validated());
    }
}
