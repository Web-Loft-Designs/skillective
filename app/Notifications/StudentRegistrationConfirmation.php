<?php

namespace App\Notifications;

use App\Models\CustomNotification;

class StudentRegistrationConfirmation extends AbstractCustomNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('student_registration_confirmation')->first();
    }
}