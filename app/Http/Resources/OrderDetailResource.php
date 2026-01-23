<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Modules\Order\Models\OrderItem
 */
class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product'   => [
                'code'  => $this->product->code,
                'name'  => $this->product->name,
                'photo' => $this->product->photo,
                'price' => $this->product->price,
            ],
            'qty'            => $this->qty,
            'normal_price'   => $this->normal_price,
            'total_price'    => $this->total_price,
            'discount_price' => $this->discount_price,
            'discount'        => $this->discount ? [
                'type'        => $this->discount->type,
                'amount'      => $this->discount->amount,
                'final_price' => $this->discount->final_price,
            ] : null,
            'final_price' => $this->final_price,
        ];
    }
}
