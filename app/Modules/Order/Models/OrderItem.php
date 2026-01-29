<?php

namespace App\Modules\Order\Models;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Order\DTOs\ProductSnapshotDTO;
use App\Modules\Product\Models\ProductDiscount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function getProductSnapshotAttribute($value): ProductSnapshotDTO
    {
        $data = is_array($value) ? $value : json_decode($value, true);

        return ProductSnapshotDTO::fromArray($data);
    }

    public function setProductSnapshotAttribute(ProductSnapshotDTO $value): void
    {
        $this->attributes['product_snapshot'] = json_encode($value->toArray());
    }
}
