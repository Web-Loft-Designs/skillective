<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Lesson;
use Log;
use NotificationChannels\Twilio\TwilioChannel;
use App\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;

class YouMayBeInterestedInVirtualLessonNotification extends AbstractCustomNotification implements ShouldQueue
{
	use Queueable;

	private $lesson;

	public $tries = 1;

	public function __construct(Lesson $lesson)
	{
		$this->lesson	= $lesson;

		parent::__construct();
	}

	public function via($notifiable)
	{
		$this->notifiable = $notifiable;
		if ($this->notifiable instanceof User && $this->notifiable->profile) {
			$deliveryChannels = collect($this->notifiable->profile->notification_methods)
				->map(function ($item) {
					if ($item == 'sms') {
						return TwilioChannel::class;
					} elseif ($item == 'email') {
						return 'mail';
					}  elseif ($item == 'whatsapp') {
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
		return [
			'id'				=> $this->lesson->id,
			'instructor_id'		=> $this->lesson->instructor->id,
			'instructor_name'	=> $this->lesson->instructor->getName(),
			'instructor_instagram'	=> $this->lesson->instructor->profile->instagram_handle,
			'lesson_date'		=> $this->lesson->start->format('Y-m-d'),
			'lesson_start_time'	=> $this->lesson->start->format('h:i a'),
			'lesson_end_time'	=> $this->lesson->end->format('h:i a'),
			'lesson_start'		=> $this->lesson->start,
			'lesson_end'		=> $this->lesson->end,
			'lesson_location'	=> 'Virtual Lesson',
			'lesson_timezone'	=> $this->lesson->timezone_id,
			'lesson_genre'		=> $this->lesson->genre->title,
			'spot_price'		=> $this->lesson->spot_price,
			'lesson_url'		=> route('lesson', ['lesson' => $this->lesson->id])
		];
	}

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('you_may_be_interested_in_virtual_lesson_notification')->first();
    }
}