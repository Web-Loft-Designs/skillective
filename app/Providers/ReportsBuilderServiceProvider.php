<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class ReportsBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('ReportsBuilder', \App\Services\ReportsBuilder::class);
        parent::register();
    }
}
