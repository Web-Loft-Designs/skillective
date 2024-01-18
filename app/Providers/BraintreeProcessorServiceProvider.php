<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class BraintreeProcessorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register(): void
    {
        $this->app->bind('BraintreeProcessor', \App\Services\BraintreeProcessor::class);
        parent::register();
    }
}
