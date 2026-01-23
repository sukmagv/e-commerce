<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Modules\Auth\Models\Customer;
use Illuminate\Support\Facades\Route;
use App\Modules\Product\Models\Product;
use Illuminate\Support\ServiceProvider;
use App\Modules\Product\Models\ProductCategory;
use App\Supports\OrderObserver;

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

        Order::observe(OrderObserver::class);

        $models = [
            'customer' => Customer::class,
            'product'  => Product::class,
            'product-category'  => ProductCategory::class,
            'order' => Order::class,
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
