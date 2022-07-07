<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\Lesson;
use App\Models\User;

class InstructorChangeTimeLessonNotification extends AbstractCustomNotification
{

	private $lesson;
    public $user;

	public function __construct(Lesson $lesson, User $user)
	{
		$this->lesson	= $lesson;
        $this->user	= $user;

		parent::__construct();
	}

    public function via($notifiable)
    {
        return ['mail'];
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
			'lesson_location'	=> $this->lesson->location,
			'lesson_genre'		=> $this->lesson->genre->title,
			'spot_price'		=> $this->lesson->spot_price,
			'lesson_url'		=> route('lesson', ['lesson' => $this->lesson->id]),
            'student_name'      => $this->user->getName(),
		];
	}

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('lesson_instructor_change_time')->first();
    }
}
