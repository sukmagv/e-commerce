<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::listen(function ($query) {
            Log::info($query->sql, [
                'bindings' => $query->bindings,
                'time' => $query->time
            ]);
        });

        $models = [
            'product'  => Product::class,
            'product-category'  => ProductCategory::class,
        ];

        foreach ($models as $key => $modelClass) {
            Route::bind($key, function ($value) use ($modelClass) {
                return $modelClass::where((new $modelClass)->getKeyName(), $value)
                    ->orWhere('code', $value)
                    ->firstOrFail();
            });
        }
    }
}
