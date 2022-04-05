<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;

class InstructorRegistrationRequestApproved extends AbstractCustomNotification
{
    /**
     * @var \App\Models\User
     */
	private $user;

	public function __construct(User $user)
	{
		$this->user = $user;

		parent::__construct();
	}

    public function variables()
    {
        return [
			'link' => route('registration.finish') . '?email='.urlencode($this->user->email).'&token='.urlencode($this->user->finish_registration_token),
//			'link' => config('app.url') . '/login'//. '/user/registration/finish?email='.urlencode($this->user->email).'&token='.urlencode($this->user->finish_registration_token),
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('instructor_registration_request_approved')->first();
    }
}