<?php

namespace App\Modules\Order\Models;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductDiscount;
use App\Supports\HasCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    protected $casts = [
        'qty' => 'integer',
        'normal_price' => 'integer',
        'discount_price' => 'integer',
        'final_price' => 'integer',
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
