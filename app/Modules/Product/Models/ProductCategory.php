<?php

namespace App\Modules\Product\Models;

use App\Supports\HasCode;
use App\Supports\HasSearch;
use App\Supports\HasSorting;
use App\Supports\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Database\Factories\ProductCategoryFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes, HasCode, HasSorting, HasSearch;

    protected $fillable = [
        'code',
        'name',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function newFactory()
    {
        return ProductCategoryFactory::new();
    }
}
