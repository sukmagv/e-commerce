<?php

namespace App\Supports;

use Illuminate\Database\Eloquent\Builder;

trait HasSearch
{
    /**
     * Search data based on input keyword
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @param string $column
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, ?string $search, string $column = 'name'): Builder
    {
        return $query->when($search, function (Builder $q) use ($column, $search) {
            $q->where($column, 'like', "%{$search}%");
        });
    }
}
