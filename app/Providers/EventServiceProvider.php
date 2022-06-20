<?php

namespace App\Providers;

use App\Listeners\LogoutSuccessful;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],
		\SocialiteProviders\Manager\SocialiteWasCalled::class => [
			'SocialiteProviders\Instagram\InstagramExtendSocialite@handle',
		],
        'Illuminate\Auth\Events\Logout' => [
            LogoutSuccessful::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
