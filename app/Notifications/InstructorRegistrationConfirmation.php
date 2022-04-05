<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;

class InstructorRegistrationConfirmation extends AbstractCustomNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('instructor_registration_confirmation')->first();
    }
}