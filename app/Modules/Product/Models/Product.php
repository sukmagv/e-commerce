<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Auth\Models\Traits\HasCode;
use App\Modules\Auth\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasCode, SoftDeletes, HasSlug;

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

    public function productDiscounts(): HasMany
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
            get: fn (?string $value) => $value ?? Storage::url($value),
        );
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when(
            $search,
            fn ($q) => $q->where('name', 'like', "%{$search}%")
        );
    }
}
