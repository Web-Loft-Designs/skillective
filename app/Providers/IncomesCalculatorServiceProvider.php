<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class IncomesCalculatorServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('IncomesCalculator', \App\Services\IncomesCalculator::class);
        parent::register();
    }
}
