<?php

namespace App\Notifications;

use App\Models\CustomNotification;

class StudentRegistrationConfirmation extends AbstractCustomNotification
{

    public function variables()
    {
        return [
            'get_started_url'  => config('app.url') . '/student/dashboard',
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('student_registration_confirmation')->first();
    }
}