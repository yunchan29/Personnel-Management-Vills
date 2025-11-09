<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Application;
use App\Observers\ApplicationObserver;

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
        // Register Application Observer for automatic status synchronization
        Application::observe(ApplicationObserver::class);
    }
}
