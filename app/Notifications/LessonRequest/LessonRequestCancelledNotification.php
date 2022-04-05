<?php

namespace App\Notifications\LessonRequest;

use App\Models\CustomNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\LessonRequest\LessonRequestNotification;

class LessonRequestCancelledNotification extends LessonRequestNotification implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('lesson_request_cancelled')->first();
    }
}