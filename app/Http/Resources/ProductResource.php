<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Modules\Product\Models\Product
 */
class ProductResource extends JsonResource
{
    protected bool $isGetDetail = false;

    /**
     * Set the instance to "get detail" mode.
     *
     * @return self
     */
    public function getDetail(): self
    {
        $this->isGetDetail = true;
        return $this;
    }

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
            'name'        => $this->name,
            'price'       => $this->price,
            'is_discount' => $this->is_discount,
            'discount'    => $this->whenLoaded('activeDiscount', fn() => [
                'type'      => $this->activeDiscount->type,
                'amount'      => $this->activeDiscount->amount,
                'final_price' => $this->activeDiscount->final_price,
            ]),

            $this->mergeWhen($this->isGetDetail, [
                'slug'     => $this->slug,
                'photo'    => $this->photo,
                'category' => $this->category->name,
            ]),
        ];
    }
}
