<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'hiddenUpcomingLessonId', // for instructors
        'hiddenUpcomingBookingId', // for students

		'instructorClientsPerPage',
		'instructorBookingsPerPage',
		'instructorLessonsPerPage',
		'instructorPayoutsPerPage',

		'studentBookingsPerPage',
		'studentInstructorsPerPage',

		'instructorRegistered',

		'lessonRequestsPerPage',

		'adminLessonsPerPage',
		'adminInstructorsPerPage',
		'adminStudentsPerPage',
		'adminGenresPerPage',
		'adminGenreCategoriesPerPage',
		'adminBookingsPerPage',
		'adminFaqCategoriesPerPage',
		'adminFaqsPerPage',

        'backToRequestLesson'
    ];
}
