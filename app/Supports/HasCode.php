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
        static::creating(fn ($model) => static::setCodeIfEmpty($model));
        static::created(fn ($model) => static::finalizeCode($model));
    }

    protected static function setCodeIfEmpty($model): void
    {
        if (!empty($model->code)) {
            return;
        }

        $model->code = static::buildCode($model);
    }

    protected static function finalizeCode($model): void
    {
        if (str_contains($model->code, '-000-')) {
            $model->updateQuietly([
                'code' => static::buildCode($model),
            ]);
        }
    }

    protected static function buildCode($model): string
    {
        $prefix = static::codePrefixes()[class_basename($model)] ?? '';

        return static::generateCode($prefix, $model);
    }

    /**
     * Generate data based on model prefix and date
     *
     * @param string $prefix
     * @return string
     */
    protected static function generateCode(string $prefix, Model $model): string
    {
        $date = Carbon::now()->format('dmy');

        return sprintf(
            '%s-%03d-%s',
            $prefix,
            $model->id,
            $date
        );
    }
}
