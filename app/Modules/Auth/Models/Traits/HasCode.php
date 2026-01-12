<?php

namespace App\Modules\Auth\Models\Traits;

use Illuminate\Support\Carbon;

trait HasCode
{
    protected static function codePrefixes(): array
    {
        return [
            'Customer' => 'CUS',
        ];
    }

    protected static function bootHasCode()
    {
        static::creating(function ($model) {
            $modelName = class_basename($model);

            $prefix = $model->codePrefixes()[$modelName];

            $model->code = static::generateCode($prefix);
        });
    }

    protected static function generateCode(string $prefix): string
    {
        $nextId = (static::max('id') ?? 0) + 1;
        $nextIdPadded = str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $date = Carbon::now()->format('Ymd');

        return sprintf('%s-%s-%s', $prefix, $nextIdPadded, $date);
    }
}
