<?php

namespace App\Console\Commands;

use App\Models\User;
use \App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Faker\Generator as Faker;
use Illuminate\Http\Request;


class SyncClientInstructors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_client_instructors {student_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "to sync client instructors checking made bookings";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $userRepository = null;
    private $faker = null;

    public function __construct(UserRepository $userRepository, Faker $faker)
    {
		$this->userRepository = $userRepository;
		$this->faker = $faker;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $student_id = $this->argument('student_id');

        if ($student_id) {
            $student = User::find($student_id);
            if ($student) {
                foreach ($student->bookings as $booking) {
                    if ($student->instructors()->where('instructor_id', $booking->instructor_id)->count() == 0) {
                        $student->instructors()->attach($booking->instructor);
                    }
                }

                foreach ($student->purchasedLessons as $purchases) {
                    if ($student->instructors()->where('instructor_id', $purchases->instructor_id)->count() == 0) {
                        $student->instructors()->attach($purchases->instructor);
                    }
                }
            }
        }else{
            $r = new Request();
            foreach ($this->userRepository->getStudents($r) as $student){

                foreach ($student->purchasedLessons as $purchases) {
                    if ($student->instructors()->where('instructor_id', $purchases->instructor_id)->count() == 0) {
                        $student->instructors()->attach($purchases->instructor);
                    }
                }

                foreach ($student->bookings as $booking) {

                    if ($student->instructors()->where('instructor_id', $booking->instructor_id)->count() == 0) {
                        $student->instructors()->attach($booking->instructor);
                    }
                }
            }

            $r2 = new Request();

            foreach ($this->userRepository->getInstructors($r2) as $instructor){

                foreach ($instructor->myPreLessonsPurchases as $purchases2) {
                    if ($instructor->clients()->where('client_id', $purchases2->student_id)->count() == 0) {
                        $instructor->clients()->attach($purchases2->student);
                    }
                }

                foreach ($instructor->myLessonsBookings as $booking2) {

                    if ($instructor->clients()->where('client_id', $booking2->student_id)->count() == 0) {
                        $instructor->clients()->attach($booking2->student);
                    }
                }
            }
        }
    }
}
