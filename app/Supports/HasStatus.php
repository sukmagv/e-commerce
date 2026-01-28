<?php

namespace App\Supports;

use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    /**
     * Get order data based on selected status
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|null $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $q) => $q->where('status', $status));
    }
}
