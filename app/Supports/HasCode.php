<?php

namespace App\Supports;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasCode
{
    /**
     * Prefix for code by models
     *
     * @return array
     */
    protected static function codePrefixes(): array
    {
        return [
            'Customer' => 'CUS',
            'ProductCategory' => 'PCA',
            'Product' => 'PRD',
            'Order' => 'ORD',
        ];
    }

    /**
     * Boot generate data method
     *
     * @return void
     */
    protected static function bootHasCode()
    {
        static::creating(function ($model) {
            if (!empty($model->code)) {
                return;
            }

            $modelName = class_basename($model);
            $prefix = static::codePrefixes()[$modelName] ?? '';

            $model->code = static::generateCode($prefix);
        });
    }

    /**
     * Generate data based on model prefix and date
     *
     * @param string $prefix
     * @return string
     */
    protected static function generateCode(string $prefix): string
    {
        $date = Carbon::now()->format('Ymd');

        $query = static::query();

        // pakai withTrashed jika model pakai SoftDeletes
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            $query->withTrashed();
        }

        $last = $query
            ->whereDate('created_at', Carbon::today())
            ->where('code', 'like', $prefix . '-%')
            ->latest('id')
            ->first();

        $lastNumber = 0;

        if ($last?->code) {
            $parts = explode('-', $last->code);
            $lastNumber = (int) ($parts[1] ?? 0);
        }

        return sprintf(
            '%s-%03d-%s',
            $prefix,
            $lastNumber + 1,
            $date
        );
    }
}
