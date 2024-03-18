<?php

namespace App\Providers;

use App\Rules\KhmerCharacters;
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
        Validator::extend('khmer_only', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\p{Khmer} ]+$/u', $value);
        });

        Validator::replacer('khmer_only', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', str_replace('_', ' ', $attribute), 'The :attribute field must contain Khmer characters only.');
        });

        Validator::extend('chinese_only', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\p{Han} ]+$/u', $value);
        });
        
        Validator::replacer('chinese_only', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', str_replace('_', ' ', $attribute), 'The :attribute field must contain Chinese characters only.');
        });
    }
}
