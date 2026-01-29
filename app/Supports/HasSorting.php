<?php

namespace App\Supports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasSorting
{
    /**
     * Apply dynamic sorting to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Request $request
     * @param array $allowedFields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByRequest(Builder $query, Request $request, array $allowed): Builder
    {
        $sortBy = $request->query('sort_by');
        $sortOrder  = $request->query('sort_order', 'asc');

        if (! $sortBy || ! in_array($sortBy, array_keys($allowed))) {
            return $query;
        }

        $sort = $allowed[$sortBy];
        $table = $query->getModel()->getTable();

        // kolom tabel sendiri
        if (! str_contains($sort, '.')) {
            return $query->orderBy("$table.$sort", $sortOrder);
        }

        // relasi (user.name)
        [$relation, $column] = explode('.', $sort);

        $relationObj = $query->getModel()->{$relation}();
        $relatedTable = $relationObj->getRelated()->getTable();
        $foreignKey   = $relationObj->getForeignKeyName();

        $query->leftJoin(
            $relatedTable,
            "$relatedTable.id",
            '=',
            "$table.$foreignKey"
        )->select("$table.*");

        return $query->orderBy("$relatedTable.$column", $sortOrder);
    }

}
