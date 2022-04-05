<?php

namespace App\Notifications\Bookings;

use App\Models\CustomNotification;
use App\Models\Booking;

class BookingCantReleaseTransactionAdminNotification extends BookingNotification
{

	public function variables()
	{
		$variables = parent::variables();
		$variables['reason'] = $this->booking->status_reason;
		return $variables;
	}
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('booking_cant_release_transaction_admin_notification')->first();
    }
}