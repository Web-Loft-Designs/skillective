<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;

class StudentRegistrationAdminNotification extends AbstractCustomNotification
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
            'user_url'        => route('backend.students.index') . '?user_id=' . $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('student_registration_for_admins')->first();
    }
}