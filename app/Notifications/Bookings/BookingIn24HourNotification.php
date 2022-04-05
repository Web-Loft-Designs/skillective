<?php

namespace App\Notifications\Bookings;

use App\Models\CustomNotification;
use App\Models\Booking;

class BookingIn24HourNotification extends BookingNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('booking_in_24_hour')->first();
    }
}