<?php

namespace App\Notifications\LessonRequest;

use App\Models\CustomNotification;
use App\Models\LessonRequest;
use App\Models\Setting;
use App\Notifications\AbstractCustomNotification;

class LessonRequestNotification extends AbstractCustomNotification
{
	/**
	 * @var \App\Models\LessonRequest
	 */
	public $lessonRequest;

	public function __construct(LessonRequest $lessonRequest)
	{
	    \Log::info($lessonRequest->id);
		$this->lessonRequest = $lessonRequest;

		parent::__construct();
	}

	public function variables()
	{
		return [
			'id'				=> $this->lessonRequest->id,
			'student_name'		=> $this->lessonRequest->student->getName(),
			'instructor_name'	=> $this->lessonRequest->instructor->getName(),
			'lesson_start'		=> $this->lessonRequest->start,
			'lesson_end'		=> $this->lessonRequest->end,
			'lesson_datetime'	=> $this->lessonRequest->start->format('M jS H:i a-') . $this->lessonRequest->end->format('H:i a') . ' ' . getTimezoneAbbrev($this->lessonRequest->timezone_id),
			'lesson_location'	=> $this->lessonRequest->location ? $this->lessonRequest->location : 'Virtual Lesson',
			'lesson_genre'		=> $this->lessonRequest->genre->title,
			'lesson_price'		=> number_format($this->lessonRequest->lesson_price, 2),
			'count_participants'		=> $this->lessonRequest->count_participants,

			'student_note'	=> $this->lessonRequest->student_note,
			'instructor_note'	=> $this->lessonRequest->instructor_note,
			'lesson_url'		=> route('lesson', ['lesson' => $this->lessonRequest->lesson_id]),
			'hours_to_book' => Setting::getValue('time_to_book_lesson_request', 24),
			'hours_to_accept' => Setting::getValue('time_to_approve_lesson_request', 24)

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