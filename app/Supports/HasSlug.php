<?php

namespace App\Supports;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot slug generator
     *
     * @return void
     */
    protected static function bootHasSlug()
    {
        static::saving(function ($model) {
            if ($model->isDirty('name')) {
                $name = $model->name ?? 'default'; // fallback jika null
                $model->slug = static::uniqueSlug($name, $model->id);
            }
        });
    }

    /**
     * Generate slug based on data name
     *
     * @param string $value
     * @param mixed $ignoreId
     * @return string
     */
    protected static function uniqueSlug(string $value, mixed $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, function ($q) use ($ignoreId) {
                    $q->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
