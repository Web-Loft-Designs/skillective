<?php

namespace App\Notifications\Admin;

use App\Notifications\AbstractCustomNotification;
use App\Models\CustomNotification;
use App\Models\User;
use App\Models\Invitation;

class InviteNewInstructorRequest extends AbstractCustomNotification //implements ShouldQueue
{
    /**
     * @var \App\Models\Invitation
     */
    private $invitation;

	public function __construct(Invitation $invitation)
	{
		$this->invitation	= $invitation;

		parent::__construct();
	}

    public function variables()
    {
    	$sender = User::find($this->invitation->invited_by);
		$senderInstagramHandle = $sender!=null ? $sender->profile->instagram_handle : null;

		$invited_contact = $this->invitation->invited_email ? $this->invitation->invited_email
			: ($this->invitation->invited_mobile_phone ? $this->invitation->invited_mobile_phone
				: ($this->invitation->invited_instagram_handle ? ('<a href="https://www.instagram.com/'.$this->invitation->invited_instagram_handle.'" target="_blank">@'.$this->invitation->invited_instagram_handle.'</a>') : 'undefined contact'));

        return [
            'sender_name'  => $sender!=null ? ($sender->getName() . ($senderInstagramHandle ? " - @$senderInstagramHandle" : '')) : 'One of our users',
            'recepient_name' => $this->invitation->invited_name,
			'invited_contact' => $invited_contact,
            'registration_url' => route('instructor.register') . '?invitation=' . $this->invitation->invitation_token,
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('invite_new_instructor_request')->first();
    }
}