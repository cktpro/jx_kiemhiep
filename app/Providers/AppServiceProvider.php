<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // (tính năng mới, không có trong code gốc) Dùng view phân trang Bootstrap 4
        // cho đồng bộ với giao diện AdminLTE 3.2 + Bootstrap 4.6 của khu vực
        // admin / đại lý.
        Paginator::useBootstrap();
    }
}
