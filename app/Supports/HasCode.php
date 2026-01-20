<?php

namespace App\Supports;

use Illuminate\Support\Carbon;

trait HasCode
{
    protected static function codePrefixes(): array
    {
        return [
            'Customer' => 'CUS',
            'ProductCategory' => 'PCA',
            'Product' => 'PRD',
            'Order' => 'ORD',
        ];
    }

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

    protected static function generateCode(string $prefix): string
    {
        $date = Carbon::now()->format('Ymd');

        // Get last record today
        $last = static::withTrashed()
            ->whereDate('created_at', Carbon::today())
            ->latest('id')
            ->first();

        $lastNumber = 0;

        if ($last && $last->code) {
            $parts = explode('-', $last->code);
            if (isset($parts[1])) {
                $lastNumber = (int) $parts[1];
            }
        }

        $nextNumber = $lastNumber + 1;

        return sprintf('%s-%03d-%s', $prefix, $nextNumber, $date);
    }
}
