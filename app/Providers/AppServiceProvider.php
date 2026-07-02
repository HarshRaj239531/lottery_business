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
        \App\Models\Installment::observe(\App\Observers\InstallmentObserver::class);
        \App\Models\Payout::observe(\App\Observers\PayoutObserver::class);
    }
}
