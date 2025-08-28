<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WebScrapingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WebScrapingService::class, function ($app) {
            return new WebScrapingService();
        });
        
        // Register Excel facade alias
        $this->app->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
