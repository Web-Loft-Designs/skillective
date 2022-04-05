<?php

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\Lesson;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class BookingsTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run(BookingRepository $bookingRepository, UserRepository $userRepository)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bookings')->truncate();
        DB::table('student_instructor')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		$faker = Faker\Factory::create('en_US');

		$request = new Request();
		$instructors = $userRepository->getInstructors($request);
//		$instructors = [\App\Models\User::find(2)];
		$students = $userRepository->getStudents($request);
		$serviceFee = (float)Setting::getValue('skillective_service_fee', '1.00');
		foreach ($instructors as $instructor){
			foreach ($instructor->lessons as $lesson){
				if ($lesson->is_cancelled==true)
					continue;
//				print $lesson->id . "\n";
				foreach ($students as $student){
					if ($faker->boolean(1) && $lesson->getCountFreeSpots()>0){
//						print "book\n";
						$status = $faker->randomElement(Booking::getStatuses());
						if ($lesson->start->timestamp<time())
							$status = $faker->randomElement([Booking::STATUS_COMPLETE, Booking::STATUS_CANCELLED]);
						else
							$status = $faker->randomElement([Booking::STATUS_PENDING, Booking::STATUS_CANCELLED, Booking::STATUS_ESCROW]);
						$booking = new Booking();
						$booking->lesson_id			= $lesson->id;
						$booking->special_request	= $faker->text(100);
						$booking->spot_price		= $lesson->spot_price;
						$booking->instructor_id		= $instructor->id;
						$booking->student_id		= $student->id;
						$booking->status			= $status;
						switch ($status){
							case Booking::STATUS_COMPLETE:
							case Booking::STATUS_ESCROW_RELEASED:
							case Booking::STATUS_ESCROW:
							case Booking::STATUS_CANCELLED:
								$booking->service_fee = $serviceFee;
								$booking->processor_fee = 0;
								$booking->transaction_id = 1234567;
								break;
						}
						$booking->saveQuietly();

						if ($instructor->id>10){
							if ($instructor->clients()->where('client_id', $student->id)->count() == 0){
								$instructor->clients()->attach($student);
							}
							if ($student->instructors()->where('instructor_id', $instructor->id)->count() == 0){
								$student->instructors()->attach($instructor);
							}
						}
					}
				}
			}
		}
//
//		$genres = Genre::all();
//		$minutesStart = range(0, 45, Lesson::TIME_INTERVAL);
//        foreach ($instructors as $instructor) {
//
////        	if ($instructor->id!=22)
////        		continue;
//
//            $countLessons = $faker->randomElement(range(30, 40));
//            for ($i = 0; $i<$countLessons; $i++){
//            	$startDate = $faker->dateTimeBetween('last month', date('Y-m-t 23:59:59'));
//            	$minuteStart = sprintf("%02d", $faker->randomElement($minutesStart));
//				$start = $startDate->format("Y-m-d H:{$minuteStart}:00");
//
//				$minutesEnd = range( ($minuteStart+5), 55, 5);
//
//
//				$endDate = $faker->dateTimeBetween($start, $startDate->format('Y-m-d 23:59:59'));
//
//				$minuteEnd = sprintf("%02d", $faker->randomElement($minutesEnd));
//				$end = $endDate->format("Y-m-d H:{$minuteEnd}:00");
//
//				$instructor->lessons()->create([
//					'genre_id' => $faker->randomElement($genres)->id,
//					'start' => $start,
//					'end' => $end,
//					'spots_count' => $faker->randomDigit,
//					'spot_price' => $faker->randomFloat(2, 10, 100),
//					'location' => $faker->address,
//					'address' => $faker->streetAddress,
//					'city' => $faker->city,
//					'state' => $faker->stateAbbr,
//					'zip' => $faker->postcode
////					'is_cancelled' => 0
//				]);
//			}
//        }
    }
}
