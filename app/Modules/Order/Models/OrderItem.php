<?php

namespace App\Modules\Order\Models;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductDiscount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'discount_id',
        'qty',
        'normal_price',
        'total_price',
        'discount_price',
        'final_price',
        'product_snapshot',
    ];

    protected $casts = [
        'qty' => 'integer',
        'normal_price' => 'float',
        'discount_price' => 'float',
        'final_price' => 'float',
        'product_snapshot' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ProductDiscount::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(ProductDiscount::class)->withTrashed();
    }
}
