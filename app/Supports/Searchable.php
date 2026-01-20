<?php

namespace App\Supports;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSearch(Builder $query, string $column = 'name', ?string $search): Builder
    {
        return $query->when($search, function (Builder $q) use ($column, $search) {
            $q->where($column, 'like', "%{$search}%");
        });
    }
}
