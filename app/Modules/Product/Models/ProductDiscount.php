<?php

namespace App\Modules\Product\Models;

use App\Modules\Product\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDiscount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'type',
        'amount',
        'final_price',
    ];

    protected $casts = [
        'type' => DiscountType::class,
        'amount' => 'integer',
        'final_price' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
