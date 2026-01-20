<?php

namespace App\Supports;

use Illuminate\Database\Eloquent\Builder;

trait HasSearch
{
    public function scopeSearch(Builder $query, ?string $search, string $column = 'name'): Builder
    {
        return $query->when($search, function (Builder $q) use ($column, $search) {
            $q->where($column, 'like', "%{$search}%");
        });
    }
}
