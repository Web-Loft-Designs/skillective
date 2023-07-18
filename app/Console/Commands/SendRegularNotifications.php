<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Models\RegularNotification;
use App\Repositories\BookingRepository;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Notifications\Bookings\BookingIn1HourNotification;
use App\Notifications\Bookings\BookingIn24HourNotification;
use App\Models\User;

class SendRegularNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send_regular_notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "send notifications to users, if less than 24 hours, and 1 hour before lesson";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $bookingRepository = null;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bookingsShouldHandeled = $this->bookingRepository->getBookingsWhereUserShouldReceiveRegularNotifications();

        foreach ($bookingsShouldHandeled as $booking) {

            $lesson = Lesson::find($booking->lesson_id);

            $lessonDateTimeUTC = Carbon::createFromFormat('Y-m-d H:i:s', $lesson->start, $lesson->timezone_id)->setTimezone('UTC');
            $serverDateTimeUTC =  Carbon::now()->setTimezone('UTC');

            $instructor = User::find($booking->instructor_id);
            $student = User::find($booking->student_id);

            $timeAtLessonTimezone = $serverDateTimeUTC->format('Y-m-d H:i:s');
            if ($serverDateTimeUTC->gte($lessonDateTimeUTC->subHour())) {
                $isStudentSended = RegularNotification::where('lesson_id', $lesson->id)
                    ->where('user_id', $student->id)
                    ->where('type', 'hourly')
                    ->first();
                $isInstructorSended = RegularNotification::where('lesson_id', $lesson->id)
                    ->where('user_id', $instructor->id)
                    ->where('type', 'hourly')
                    ->first();


                if (!$isStudentSended) {
                    $model = new RegularNotification([
                        'user_id' => $student->id,     'lesson_id' => $lesson->id,
                        'booking_id' => $booking->id,  'type' => 'hourly',
                        'status' => 'success',         'message' => '', 'sended_at' => $timeAtLessonTimezone,
                        'date_send_time_utc' => $serverDateTimeUTC
                    ]);
                    $model->save();
                    $student->notify(new BookingIn1HourNotification($booking));
                }

                if (!$isInstructorSended) {
                    $model = new RegularNotification([
                        'user_id' => $instructor->id,  'lesson_id' => $lesson->id,
                        'booking_id' => $booking->id,  'type' => 'hourly',
                        'status' => 'success',         'message' => '',
                        'sended_at' => $timeAtLessonTimezone, 'date_send_time_utc' => $serverDateTimeUTC
                    ]);
                    $model->save();
                    $instructor->notify(new BookingIn1HourNotification($booking));
                }
            }

            if ($serverDateTimeUTC->gt($lessonDateTimeUTC->subDay())) {

                $isStudentSended = RegularNotification::where('lesson_id', $lesson->id)
                    ->where('user_id', $student->id)
                    ->where('type', 'daily')
                    ->first();
                $isInstructorSended = RegularNotification::where('lesson_id', $lesson->id)
                    ->where('user_id', $instructor->id)
                    ->where('type', 'daily')
                    ->first();
                
                if (!$isStudentSended) {
                    $model = new RegularNotification([
                        'user_id' => $student->id,            'lesson_id' => $lesson->id,
                        'booking_id' => $booking->id,         'type' => 'daily',
                        'status' => 'success',                'message' => '',
                        'sended_at' => $timeAtLessonTimezone, 'date_send_time_utc' => $serverDateTimeUTC
                    ]);
                    $model->save();
                    $student->notify(new BookingIn24HourNotification($booking));
                }


                if (!$isInstructorSended) {
                    $model = new RegularNotification([
                        'user_id' => $instructor->id,         'lesson_id' => $lesson->id,
                        'booking_id' => $booking->id,         'type' => 'daily',
                        'status' => 'success',                'message' => '',
                        'sended_at' => $timeAtLessonTimezone, 'date_send_time_utc' => $serverDateTimeUTC
                    ]);
                    $model->save();
                    $instructor->notify(new BookingIn24HourNotification($booking));
                }
            }

        }
    }
}
