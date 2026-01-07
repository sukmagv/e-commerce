<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource['token'],
            'user_id' => $this->resource['user']->id,
            'code' => $this->resource['customer']->code,
            'email' => $this->resource['user']->email,
            'name' => $this->resource['user']->name,
            'phone' => $this->resource['customer']->phone,
            'photo' => $this->resource['customer']->photo,
        ];
    }
}
