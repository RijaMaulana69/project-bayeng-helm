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

        // View Composer for Admin Layout (Dynamic badges across all admin pages)
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $pickupCount = \App\Models\Pesanan::whereIn('status', ['ready', 'unpaid_pickup'])->count();
            $pendingCount = \App\Models\Pesanan::where('status', 'pending')->count();
            $criticalStockCount = \App\Models\VarianHelm::where('stok', '<=', 3)->count();
            $chatUnreadCount = \Illuminate\Support\Facades\Schema::hasTable('chat_percakapan')
                ? (int) \App\Models\ChatPercakapan::sum('unread_karyawan')
                : 0;

            $view->with([
                'nav_pickup_count' => $pickupCount,
                'nav_pending_count' => $pendingCount,
                'nav_transaksi_count' => $pickupCount + $pendingCount,
                'nav_critical_stock_count' => $criticalStockCount,
                'nav_chat_unread_count' => $chatUnreadCount,
            ]);
        });

        // Session cookie security
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
