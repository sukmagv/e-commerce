<?php

namespace App\Http\Requests;

use App\Supports\BaseRequest;
use Illuminate\Validation\Rule;
use App\Modules\Order\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;

class QueryParamRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search'     => ['sometimes', 'string'],
            'status'     => ['sometimes', 'string', Rule::enum(OrderStatus::class)],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date'   => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'limit'      => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
