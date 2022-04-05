<?php

namespace App\Notifications\Bookings;

use App\Models\CustomNotification;
use App\Notifications\Bookings\BookingNotification;
use App\Models\Booking;
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;
use Illuminate\Notifications\Messages\MailMessage;

class BookingApprovedStudentNotification extends BookingNotification
{
	private $icalendar;

	public function __construct(Booking $booking)
	{

			$lesson = $booking->lesson;
			$event = Event::create()
							 ->name("{$lesson->genre->title} lesson at {$lesson->city}, {$lesson->state} (#{$booking->id})")
							 ->uniqueIdentifier("skillective_{$booking->id}")
							 ->location($lesson->location)
							 ->startsAt(new \DateTime($lesson->start, new \DateTimeZone($lesson->timezone_id)))
							 ->endsAt(new \DateTime($lesson->end, new \DateTimeZone($lesson->timezone_id)));
//							 ->withTimezone();
//							 ->coordinates($lesson->lat, $lesson->lng);
		$this->icalendar = Calendar::create("$lesson->genre->title} lesson")
								   ->event($event);

		parent::__construct($booking);
	}

	public function toMail($notifiable): MailMessage
	{
		$mailMessage = parent::toMail($notifiable);
		if ($this->icalendar) {
			$mailMessage = $mailMessage
				->attachData($this->icalendar->toString(), 'booked-lesson.ics', ['mime' => 'text/calendar']);
		}

		return $mailMessage;
	}

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('booking_approved_student_notification')->first();
    }
}