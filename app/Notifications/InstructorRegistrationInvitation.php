<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;
use App\Models\Invitation;
//use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\TwilioChannel;
use App\Channels\WhatsAppChannel;

class InstructorRegistrationInvitation extends AbstractCustomNotification //implements ShouldQueue
{
    /**
     * @var User
     */
    private $invitation;

	private $use_methods;

	public function __construct(Invitation $invitation, $use_methods)
	{
		$this->invitation	= $invitation;
		$this->use_methods	= $use_methods;

		parent::__construct();
	}

	public function via($notifiable)
	{
		$this->notifiable = $notifiable;

		if ($this->notifiable instanceof Invitation) {
			$deliveryChannels = collect( $this->use_methods )
				->map(function ($item) {
					if ($item == 'sms') {
						return TwilioChannel::class;
					} elseif ($item == 'email') {
						return 'mail';
					}
					elseif ($item == 'whatsapp') {
						return WhatsAppChannel::class;
					}
					return $item;
				})->toArray();
			return $deliveryChannels;
		}
		return [];
	}

    public function variables()
    {
    	$sender = User::find($this->invitation->invited_by);
		$senderInstagramHandle = $sender!=null ? $sender->profile->instagram_handle : null;
        return [
            'sender_name'  => $sender!=null ? ($sender->getName() . ($senderInstagramHandle ? " - @$senderInstagramHandle" : '')) : 'One of our users',
            'recepient_name' => $this->invitation->invited_name ? ", " . $this->invitation->invited_name : '',
            'registration_url' => route('instructor.register') . '?invitation=' . $this->invitation->invitation_token,
        ];
    }

    /**
     * @return CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('instructor_invitation')->first();
    }
}
