<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
	/**
	 * Send the given notification.
	 *
	 * @param  mixed  $notifiable
	 * @param  \Illuminate\Notifications\Notification  $notification
	 * @return void
	 */
	public function send($notifiable, Notification $notification)
	{
		$message = $notification->toWhatsApp($notifiable);

		// Send notification to the $notifiable instance...
	}
}