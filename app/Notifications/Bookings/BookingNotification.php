<?php

namespace App\Notifications\Bookings;

use App\Models\CustomNotification;
use App\Models\Booking;
use App\Models\Setting;
use App\Notifications\AbstractCustomNotification;

class BookingNotification extends AbstractCustomNotification
{
	/**
	 * @var \App\Models\Booking
	 */
	public $booking;

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
			'lesson_start'		=> $this->booking->lesson->start->format('h:i A'),
			'lesson_end'		=> $this->booking->lesson->end->format('h:i A'),
			'lesson_datetime'	=> $this->booking->lesson->start->format('M jS h:i A-') . $this->booking->lesson->end->format('h:i A') . ' ' . getTimezoneAbbrev($this->booking->lesson->timezone_id),
			'lesson_location'	=> $this->booking->lesson->location ? $this->booking->lesson->location : 'Virtual Lesson',
			'lesson_genre'		=> $this->booking->lesson->genre->title,
			'spot_price'		=> number_format($this->booking->spot_price, 2),
			'total_fee'			=> number_format(($this->booking->service_fee+$this->booking->processor_fee+$this->booking->virtual_fee), 2),
			'to_pay'			=> number_format(($this->booking->spot_price + $this->booking->service_fee + $this->booking->processor_fee + $this->booking->virtual_fee), 2),
			'special_request'	=> $this->booking->special_request,
			'booking_url'		=> route('instructor.bookings') . '?booking=' . $this->booking->id
									// for request cancellation email
									. ( (!in_array($this->booking->status, [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETE]) && $this->booking->has_cancellation_request==1) ? '&type=pending_cancellation' : ''),
			'lesson_url'		=> route('lesson', ['lesson' => $this->booking->lesson_id]),
			'time_to_approve_booking' => Setting::getValue('time_to_approve_booking', 48)
		];
	}
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return new CustomNotification;
    }
}