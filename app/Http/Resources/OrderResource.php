<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Modules\Order\Models\Order
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->code,
            'username'    => $this->user->name,
            'sub_total'   => $this->sub_total,
            'tax_amount'  => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'status'      => $this->status,
            'proof_link'  => $this->payment?->latestProof->proof_link,
            'updated_at'  => $this->created_at,

            'item' => OrderDetailResource::make($this->whenLoaded('orderItem')),
        ];
    }
}
