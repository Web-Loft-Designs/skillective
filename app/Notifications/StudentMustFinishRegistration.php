<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;

class StudentMustFinishRegistration extends AbstractCustomNotification
{
    /**
     * @var \App\Models\User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->student = $user;

        parent::__construct();
    }

    public function variables()
    {
        return [
            'link' => route('registration.finish') . '?email='.urlencode($this->student->email).'&token='.urlencode($this->student->finish_registration_token),
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('user_must_finish_registration')->first();
    }
}