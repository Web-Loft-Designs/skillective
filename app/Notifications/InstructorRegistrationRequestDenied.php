<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;

class InstructorRegistrationRequestDenied extends AbstractCustomNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('instructor_registration_request_denied')->first();
    }
}