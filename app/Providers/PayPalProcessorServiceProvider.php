<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class PayPalProcessorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register(): void
    {
        $this->app->bind('PayPalProcessor', \App\Services\PayPalProcessor::class);
        parent::register();
    }
}
