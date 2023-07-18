<?php

namespace App\Notifications\Bookings;

use App\Models\CustomNotification;

class BookingIn1HourNotification extends BookingNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('booking_in_1_hour')->first();
    }
}