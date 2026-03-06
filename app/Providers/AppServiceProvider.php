<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\DataInternal;
use App\Models\Setting;

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
        View::composer('*', function ($view) {
            $requestedCount = DataInternal::where('status', 'locked')->where('is_requested', 1)->count();
            $adminPhone = Setting::where('key', 'admin_phone')->first()->value ?? '6281234567890';
            $view->with('requestedCount', $requestedCount);
            $view->with('adminPhone', $adminPhone);
        });
    }
}
