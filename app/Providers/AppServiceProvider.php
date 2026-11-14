<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Route model binding for custom primary keys
        \Illuminate\Support\Facades\Route::model('produk', \App\Models\Helm::class);
        \Illuminate\Support\Facades\Route::model('pesanan', \App\Models\Pesanan::class);

        // Session cookie security
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
