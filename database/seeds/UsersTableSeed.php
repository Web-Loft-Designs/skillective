<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersTableSeed extends Seeder
{
	public function run(){

//		DB::statement( 'SET FOREIGN_KEY_CHECKS=0;' );
//		DB::table('model_has_permissions')->truncate();
//		DB::table('model_has_roles')->truncate();
//
//		DB::table('social_logins')->truncate();
//		DB::table('users')->truncate();
//		DB::table('profiles')->truncate();
//		DB::table('user_genre')->truncate();
//		DB::table('password_resets')->truncate();
//		DB::table('user_geo_locations')->truncate();
//		DB::table('media')->truncate();
//
//		DB::table('jobs')->truncate();
//		DB::statement( 'SET FOREIGN_KEY_CHECKS=1;' );

return;
		$superAdminRole = Role::findByName('Admin');

		$u = User::create([
			'email' => 'skillective.test+admin@gmail.com',
			'first_name' => 'Admin',
			'last_name' => 'Admin',
			'password' => 'testing123'
		]);
		$u->status = 'active';
		$u->assignRole($superAdminRole);
		$u->save();

		$profileData = $this->getProfileData();
		$profile = new Profile($profileData);
		$u->profile()->save($profile);

//		$u = User::create([
//			'email' => 'admin@skillective.com',
//			'first_name' => 'Louis',
//			'last_name' => 'Darrouzet',
//			'password' => 'skil#+admin)'
//		]);
//		$u->status = 'active';
//		$u->assignRole($superAdminRole);
//		$u->save();
//
//		$profileData = $this->getProfileData();
//		$profile = new Profile($profileData);
//		$u->profile()->save($profile);
//
//
//		$u = User::create([
//			'email' => 'skillective.test@yahoo.com',
//			'first_name' => 'Skillective',
//			'last_name' => 'Admin',
//			'password' => 'live091pas==#'
//		]);
//		$u->status = 'active';
//		$u->assignRole($superAdminRole);
//		$u->save();
//
//		$profileData = $this->getProfileData();
//		$profile = new Profile($profileData);
//		$u->profile()->save($profile);



		$password = 'testing123';
		$faker = Faker\Factory::create('en_US');
		$rolesToUse = Role::whereIn('name', ['Instructor', 'Student'])->get();
		$genresIds = \App\Models\Genre::all()->pluck('id')->all();
		foreach ($rolesToUse as $userRole) {
			for ($i = 1; $i <= 1; $i++) {
				$user_data = [
					'first_name' => ucwords($faker->firstName())
					, 'last_name' => ucwords($faker->lastName())
					, 'email' => 'skillective.test+' . strtolower(str_replace([' ', '/', '_'], '.', $userRole->name)) . ".$i@gmail.com"
					, 'password' => $password
				];
				$u = User::create($user_data);
				$u->assignRole($userRole);
				$u->status = 'active';
				$u->save();
				$u->created_at = $faker->dateTimeBetween('-40 months', '-2 months');
				$u->save();
				$profileData = $this->getProfileData();
				$profile = new Profile($profileData);
				$u->profile()->save($profile);

				$count_genres = $faker->numberBetween(2, 12);
				$u->genres()->sync(array_unique($faker->randomElements($genresIds, $count_genres)));
			}
		}
	}

	private function getProfileData(){
		$faker = Faker\Factory::create('en_US');
		return [
			'address' => $faker->streetAddress,
			'city' => $faker->city,
			'state' => $faker->stateAbbr,
			'zip' => $faker->postcode,
			'mobile_phone' => '375298859083', // preg_replace('/-|(x\d+)/', '', $faker->phoneNumber)
			'dob' => $faker->date($format = 'Y-m-d', $max = '-20 years'),
			'about_me' => $faker->text(200),
			'gender' => 'male',
			'instagram_handle' => $faker->word,
			'instagram_followers_count' => $faker->numberBetween(10, 999999)
		];
	}
}
