<?php

use Illuminate\Database\Seeder;
use App\Models\Genre;
use App\Models\Lesson;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class LessonsTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run(UserRepository $userRepository)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('lessons')->truncate();
		DB::table('bookings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		$r = new Request();
		$instructors = $userRepository->getInstructors($r);
		$faker = Faker\Factory::create('en_US');

		// addresses from https://www.bestrandoms.com/random-address
		$predefinedAddresses = [
			'84 Central Ave Groton, Connecticut(CT), 06340',
			'11710 Saxon Place Ct Cypress, Texas(TX), 77433',
			'16136 Cloverton Ln Williamsport, Maryland(MD), 21795',
			'214 W 7th St Smackover, Arkansas(AR), 71762',
			'Po Box 334 Snowflake, Arizona(AZ), 85937',
			'171 County Rd #105 # B Walnut, Mississippi(MS), 38683',
			'901 S 1st St Sherman, Illinois(IL), 62684',
			'Po Box 244 Monticello, Maine(ME), 04760',
			'10 Jeffrey Ln Hightstown, New Jersey(NJ), 08520',
			'506 A Oak St NE #A A Elkader, Iowa(IA), 52043',
			'122 Lanier Dr Statesboro, Georgia(GA), 30458',
			'115 Ridge Ave SW Winter Haven, Florida(FL), 33880',
			'25216 W Buena Park Rd Lake Villa, Illinois(IL), 60046',
			'2113 Fox Rd Memphis, Indiana(IN), 47143',
			'2030 Camel St Las Vegas, Nevada(NV), 89115',
			'357 Nicholas Ln Lincoln, Alabama(AL), 35096',
			'204 Schwenk Rd Perkiomenville, Pennsylvania(PA), 18074',
			'812 Yegua St Bryan, Texas(TX), 77801',
			'15598 N 493rd Rd Tahlequah, Oklahoma(OK), 74464',
			'502 Vine St Archbold, Ohio(OH), 43502',
			'4025 Browne Ct Conley, Georgia(GA), 30288',
			'604 Center St Norwalk, Iowa(IA), 50211',
			'5 Springdale Dr York, South Carolina(SC), 29745',
			'532 Lonesome Oak Dr Copperas Cove, Texas(TX), 76522',
			'7 Powder Horn Hl Weston, Connecticut(CT), 06883',
			'295 Tarbell Rd Chester, Vermont(VT), 05143',
			'8111 E 25th Hwy Belleview, Florida(FL), 34420',
			'906 W Jenkins Ave Chewelah, Washington(WA), 99109',
			'145-E Rr 2 Guys Mills, Pennsylvania(PA), 16327',
			'3240 N Galloway Ave #APT 212 Mesquite, Texas(TX), 75150',
			'117 NE 991st Knob Noster, Missouri(MO), 65336',
			'4201 Ridgetop Trl Ellenwood, Georgia(GA), 30294',
			'208 Pine St Renovo, Pennsylvania(PA), 17764',
			'1689 Colegate Dr #APT 129 Marietta, Ohio(OH), 45750',
			'31 Grier St Warminster, Pennsylvania(PA), 18974',
			'1440 Acres Subdivision Pne West Point, Mississippi(MS), 39773',
		];

		$genres = Genre::all();
		$minutesStart = range(0, 45, Lesson::TIME_INTERVAL);
		$countSpots = range(1, 3);
        foreach ($instructors as $instructor) {

        	if ($instructor->id>15)
        		continue;

            $countLessons = ($instructor->id<10) ? $faker->randomElement(range(50, 100)) : $faker->randomElement(range(5, 10));
            for ($i = 0; $i<$countLessons; $i++){
            	$startDate = ( $i<($countLessons/2) ) ? $faker->dateTimeBetween($instructor->created_at->format('Y-m-d'), '+5 months') : $faker->dateTimeBetween('-2 months', '+5 months');
            	$minuteStart = sprintf("%02d", $faker->randomElement($minutesStart));
				$start = $startDate->format("Y-m-d H:{$minuteStart}:00");

				$minutesEnd = range( ($minuteStart+5), 55, 5);

				$endDate = $faker->dateTimeBetween($start, $startDate->format('Y-m-d 23:59:59'));

				$minuteEnd = sprintf("%02d", $faker->randomElement($minutesEnd));
				$end = $endDate->format("Y-m-d H:{$minuteEnd}:00");

				try{
					$location = $faker->randomElement($predefinedAddresses);
					$instructor->lessons()->create([
						'genre_id' => $faker->randomElement($genres)->id,
						'start' => $start,
						'end' => $end,
						'spots_count' => $faker->randomElement($countSpots),
						'spot_price' => $faker->randomFloat(2, 10, 100),
						'location' => $location,//$faker->address,
						'description' => $faker->text(100),
						'is_cancelled' => $faker->boolean(15)
					]);
				}catch (\Exception $e){
					print $e->getMessage() . ' : ' . $location . "\r\n";
				}
			}
        }
    }
}
