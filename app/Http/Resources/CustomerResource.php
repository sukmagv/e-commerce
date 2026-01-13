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
            'token'   => $this->token,
            'user_id' => $this->user->id,
            'code'    => $this->code,
            'email'   => $this->user->email,
            'name'    => $this->user->name,
            'phone'   => $this->phone,
            'photo'   => $this->photo,
        ];
    }
}
