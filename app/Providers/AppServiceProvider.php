<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        Validator::extend('verified', function ($attribute, $value, $parameters, $validator) {
            return DB::table('otps')
                ->where('id', $value)
                ->whereNotNull('verified_at')
                ->exists();
        });

        Validator::replacer('verified', function ($message, $attribute, $rule, $parameters) {
            return "The {$attribute} is not verified.";
        });
    }
}
