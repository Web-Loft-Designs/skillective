<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class UserRegistratorServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('UserRegistrator', function ($app) {
            return new \App\Services\UserRegistrator;
        });
        parent::register();
    }
}
