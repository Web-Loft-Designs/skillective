<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\Booking;

class StudentBookingConfirmation extends AbstractCustomNotification
{
	/**
	 * @var \App\Models\Booking
	 */
	private $booking;

	public function __construct(Booking $booking)
	{
		$this->booking = $booking;

		parent::__construct();
	}

	public function variables()
	{
		return [
			'id'				=> $this->booking->id,
			'student_name'		=> $this->booking->student->getName(),
			'instructor_name'	=> $this->booking->instructor->getName(),
			'lesson_start'		=> $this->booking->lesson->start,
			'lesson_end'		=> $this->booking->lesson->end,
			'lesson_location'	=> $this->booking->lesson->location,
			'lesson_genre'		=> $this->booking->lesson->genre->title,
			'spot_price'		=> $this->booking->spot_price,
			'special_request'	=> $this->booking->special_request
		];
	}
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('student_booking_request_sent')->first();
    }
}