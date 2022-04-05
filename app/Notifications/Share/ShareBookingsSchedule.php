<?php

namespace App\Notifications\Share;

use App\Models\Booking;
use App\Models\CustomNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;
use Illuminate\Http\Request;
use App\Repositories\BookingRepository;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\AbstractCustomNotification;

class ShareBookingsSchedule extends AbstractCustomNotification
{
	/**
	 * @var \App\Models\User
	 */
	private $user;

	private $icalendar;

	public function __construct(User $user, BookingRepository $bookingRepository)
	{
		$this->user = $user;

		parent::__construct();

		$request = new Request([]);
		$bookings = $bookingRepository->getStudentBookings($request, $user->id); // payed, approved, future bookings
		/**
		 * @var $booking Booking
		 */
		$events = [];
		foreach ($bookings as $booking){
			$lesson = $booking->lesson;
			$events[] = Event::create()
							 ->name("{$lesson->genre->title} lesson at {$lesson->city}, {$lesson->state} (#{$booking->id})")
							 ->uniqueIdentifier("skillective_{$booking->id}")
							 ->location($lesson->location)
							 ->startsAt(new \DateTime($lesson->start, new \DateTimeZone($lesson->timezone_id)))
							 ->endsAt(new \DateTime($lesson->end, new \DateTimeZone($lesson->timezone_id)));
//							 ->withTimezone();
//							 ->coordinates($lesson->lat, $lesson->lng);
		}
		$this->icalendar = Calendar::create('Skillective ' . $user->getName() . ' bookings')
				->event($events);
	}

	public function toMail($notifiable): MailMessage
	{
		$mailMessage = parent::toMail($notifiable);
		if ($this->icalendar) {
			$mailMessage = $mailMessage
				->attachData($this->icalendar->toString(), 'bookings-schedule.ics', ['mime' => 'text/calendar']);
		}

		return $mailMessage;
	}

	/**
	 * @return \App\Models\CustomNotification
	 */
	protected function getCustomNotificationClass(): CustomNotification
	{
		return CustomNotification::query()->whereTag('share_bookings_schedule')->first();
	}

	public function variables()
	{
		return [
			'user_name'           => $this->user->getName()
		];
	}
}