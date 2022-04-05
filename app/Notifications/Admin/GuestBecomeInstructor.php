<?php

namespace App\Notifications\Admin;

use App\Notifications\AbstractCustomNotification;
use App\Models\CustomNotification;

class GuestBecomeInstructor extends AbstractCustomNotification //implements ShouldQueue
{
    /**
     * @var \App\Models\Invitation
     */
    private $guest_email;

	public function __construct($email)
	{
		$this->guest_email	= $email;

		parent::__construct();
	}

    public function variables()
    {
        return [
            'email' => $this->guest_email
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('guest_become_instructor')->first();
    }
}