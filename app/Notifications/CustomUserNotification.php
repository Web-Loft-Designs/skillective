<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Twilio\TwilioChannel;
use App\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;

class CustomUserNotification extends AbstractCustomNotification implements ShouldQueue
{
	use Queueable;

	private $customMessage;
	private $sender;
	private $use_methods;
	public $tries = 1;

	public function __construct($message, $sender, $use_methods)
	{
		$this->customMessage	= $message;
		$this->sender			= $sender;
		$this->use_methods		= $use_methods;

		parent::__construct();
	}

	public function via($notifiable)
	{
		$this->notifiable = $notifiable;

		if ($this->notifiable instanceof User && $this->notifiable->profile) {
			$deliveryChannels = collect(array_intersect($this->use_methods, $this->notifiable->profile->notification_methods))
				->map(function ($item) {
					if ($item == 'sms') {
						return TwilioChannel::class;
					} elseif ($item == 'email') {
						return 'mail';
					} elseif ($item == 'whatsapp') {
						return WhatsAppChannel::class;
					}

					return $item;
				})->toArray();
			//Log::info('$deliveryChannels', $deliveryChannels);
			return $deliveryChannels;
		}
		return [];
	}

	public function variables()
	{
		return [
			'message'			=> $this->customMessage,
			'sender_first_name'	=> $this->sender->first_name,
			'sender_last_name'	=> $this->sender->last_name
		];
	}

	protected function getCustomNotificationClass(): CustomNotification
	{
		return CustomNotification::query()->whereTag('custom_user_notification')->first();
	}
}
