<?php

namespace App\Notifications\Bookings;

use App\Models\CustomNotification;
use App\Models\Booking;

class BookingCreatedStudentConfirmation extends BookingNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('booking_on_review_student_notification')->first();
    }
}