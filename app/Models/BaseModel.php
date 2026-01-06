<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BaseModel extends Model
{
    protected static function booted()
    {
        static::creating(function ($model) {
            if (!property_exists($model, 'code') || !empty($model->code)) {
                return;
            }

            $name = $model->name
                ?? ($model->initial_from_relation['relation'] ?? null
                    ? optional($model->loadMissing($model->initial_from_relation['relation'])
                        ->getRelationValue($model->initial_from_relation['relation']))
                        ->{$model->initial_from_relation['field'] ?? 'name'}
                    : null);

            if (!$name) {
                throw new InvalidArgumentException("Cannot generate code: 'name' is required.");
            }

            $prefix = $model->code_prefix ?? throw new InvalidArgumentException("Cannot generate code: 'code_prefix' is required.");

            $model->code = static::generateCode($name, $prefix);
        });
    }

    public static function generateCode(string $name, string $prefix): string
    {
        $prefix = strtoupper(trim($prefix));
        $initials = collect(explode(' ', strtoupper(trim($name))))
            ->filter(fn($w) => preg_match('/[A-Z]/', $w))
            ->map(fn($w) => $w[0])
            ->join('');

        return sprintf('%s-%s-%03d', $prefix, $initials ?: 'XX', static::max('id') + 1);
    }
}
