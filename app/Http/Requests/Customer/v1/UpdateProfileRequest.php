<?php

namespace App\Http\Requests\Customer\v1;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

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
            'name'  => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'min:11', 'regex:/^[0-9]+$/'],
            'photo' => ['sometimes', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
