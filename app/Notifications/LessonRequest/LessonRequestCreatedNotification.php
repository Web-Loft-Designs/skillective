<?php

namespace App\Notifications\LessonRequest;

use App\Models\CustomNotification;

class LessonRequestCreatedNotification extends LessonRequestNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('lesson_request_created')->first();
    }
}