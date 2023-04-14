<?php

use Illuminate\Database\Seeder;
use App\Models\Invitation;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class InvitationsTableSeed extends Seeder
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
        DB::table('invitations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		$r = new Request();
		$instructors = $userRepository->getInstructors($r);
		$students = $userRepository->getStudents($r);
		$faker = Faker\Factory::create('en_US');

        foreach ($instructors as $instructor) {
        	if ($faker->boolean(15)){
				foreach ($instructors as $invited) {
					if ($instructor->id != $invited->id && $faker->boolean(10)){
						$input = [
							'invited_by'			=> $instructor->id,
							'invited_name'			=> '',
							'invited_email'			=> $invited->email,
							'invited_as_instructor' => true,
							'invited_user_id'		=> $invited->id
						];
						$invitation = new Invitation($input);
						$invitation->save();
					}
				}
			}
        }
    }
}
