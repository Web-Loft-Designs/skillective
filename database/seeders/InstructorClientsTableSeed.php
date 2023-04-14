<?php

use Illuminate\Database\Seeder;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class InstructorClientsTableSeed extends Seeder
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
        DB::table('instructor_client')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		$r = new Request();

		$instructors = $userRepository->getInstructors($r);
//		$instructors = [\App\Models\User::find(2)];
		$students = $userRepository->getStudents($r)->pluck('id')->toArray();
		$faker = Faker\Factory::create('en_US');

        foreach ($instructors as $instructor) {
        	$maxCount = count($students);
            $instructorStudents = $faker->randomElements($students, $faker->randomElement(range(1, $maxCount<20?$maxCount:20)));
			$instructor->clients()->sync(array_unique($instructorStudents));
        }
    }
}
