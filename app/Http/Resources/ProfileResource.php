<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->customer?->phone,
            'photo' => $this->customer && $this->customer->photo
                ? Storage::url($this->customer->photo)
                : null,
            'role' => [
                'slug' => $this->role->slug,
                'name' => $this->role->name,
            ],
        ];
    }
}
