<?php

namespace App\Providers;

use App\Contracts\ExchangeRateProviderInterface;
use App\Services\ExchangeRateService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExchangeRateProviderInterface::class, ExchangeRateService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
