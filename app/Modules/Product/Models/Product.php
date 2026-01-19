<?php

namespace App\Modules\Product\Models;

use App\Supports\HasCode;
use App\Supports\HasSearch;
use App\Supports\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasCode, SoftDeletes, HasSlug, HasSearch;

    /** @var string */
    const IMAGE_PATH = 'product/';

    protected $fillable = [
        'category_id',
        'code',
        'slug',
        'name',
        'photo',
        'price',
        'is_discount',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_discount' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(ProductDiscount::class);
    }

    public function activeDiscount(): HasOne
    {
        return $this->hasOne(ProductDiscount::class)->latest();
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) =>
                $value ?? Storage::url(self::IMAGE_PATH . $value)
            );
    }
}
