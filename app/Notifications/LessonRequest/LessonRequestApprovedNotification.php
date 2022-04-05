<?php

namespace App\Notifications\LessonRequest;

use App\Models\CustomNotification;
use App\Models\LessonRequest;
use App\Models\Lesson;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\LessonRequest\LessonRequestNotification;

class LessonRequestApprovedNotification extends LessonRequestNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('lesson_request_approved')->first();
    }
}