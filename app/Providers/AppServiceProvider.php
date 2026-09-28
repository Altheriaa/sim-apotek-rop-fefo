<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use App\View\Components\header\NotificationDropdown;
use App\View\Components\header\UserDropdown;

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
        Paginator::defaultView('vendor.pagination.tailwind');

        // Register components with explicit lowercase namespace for case-sensitive environments (Linux)
        if (class_exists(NotificationDropdown::class)) {
            Blade::component('header.notification-dropdown', NotificationDropdown::class);
        }

        if (class_exists(UserDropdown::class)) {
            Blade::component('header.user-dropdown', UserDropdown::class);
        }
    }
}
