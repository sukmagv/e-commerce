<?php

namespace App\Supports;

use Illuminate\Database\Eloquent\Builder;

trait HasDateBetween
{
    /**
     * Get order data between two selected date
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateBetween(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder {
        return $query
            ->when($startDate,fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate,fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate));
    }
}
