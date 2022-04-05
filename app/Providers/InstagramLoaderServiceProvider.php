<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class InstagramLoaderServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('InstagramLoader', \App\Services\InstagramLoader::class);
        parent::register();
    }
}
