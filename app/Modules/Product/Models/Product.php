<?php

namespace App\Modules\Product\Models;

use App\Supports\HasCode;
use App\Supports\HasSlug;
use App\Supports\HasSearch;
use Illuminate\Support\Arr;
use App\Modules\Order\Models\OrderItem;
use App\Supports\HasDateBetween;
use App\Supports\HasSorting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasCode, SoftDeletes, HasSlug, HasSearch, HasSorting, HasDateBetween;

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
        'price' => 'float',
        'is_discount' => 'boolean',
        'is_active' => 'boolean',
    ];

    /** @var string */
    const IMAGE_PATH = 'products/';

    protected const SNAPSHOT_FIELDS = [
        'code',
        'category_id',
        'slug',
        'name',
        'photo',
        'price',
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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Accessor for the `photo` attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) =>
                $value ? asset(Storage::url(self::IMAGE_PATH . $value)) : null
        );
    }

    /**
     * Get product data with active status
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get selected product snapshot fields
     *
     * @return array
     */
    public function snapshot(): array
    {
        $data = Arr::only($this->toArray(), self::SNAPSHOT_FIELDS);

        $data['category'] = $this->category ? $this->category->only([
            'code', 'name',
        ]) : null;

        return $data;
    }
}
