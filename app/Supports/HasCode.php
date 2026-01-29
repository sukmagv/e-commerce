<?php

namespace App\Supports;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
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
        $getCode = function ($model) {
            $prefix = static::codePrefixes()[class_basename($model)] ?? '';
            return static::generateCode($prefix, $model);
        };

        static::creating(fn ($model) => $model->code ??= $getCode($model));

        static::created(fn ($model) => $model->updateQuietly(['code' => $getCode($model)]));

    }

    /**
     * Generate data based on model prefix and date
     *
     * @param string $prefix
     * @return string
     */
    protected static function generateCode(string $prefix, Model $model): string
    {
        $date = Carbon::now()->format('Ymd');

        return sprintf(
            '%s-%03d-%s',
            $prefix,
            $model->id,
            $date
        );
    }
}
