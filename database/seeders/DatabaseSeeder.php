<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	return;
		Model::unguard();

		if (config('app.debug')) {
			DB::statement( 'SET FOREIGN_KEY_CHECKS=0;' );

            $this->_truncateTables();

//			$this->call(SettingsTableSeed::class);
//			$this->call(GenresTableSeed::class);
//			$this->call(PagesDataSeed::class);
//			$this->call(TestimonialsTableSeed::class);
			$this->call(NotificationTableSeed::class);

			$this->call(RoleTableSeeder::class);
			$this->call(UsersTableSeed::class);
			DB::statement( 'SET FOREIGN_KEY_CHECKS=0;' );

			$this->call(LessonsTableSeed::class);
			$this->call(InstructorClientsTableSeed::class);
			$this->call(BookingsTableSeed::class);
			$this->call(InvitationsTableSeed::class);

			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
		}
    }

	private function _truncateTables(){
		DB::table('permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('roles')->truncate();
	}
}

class RoleTableSeeder extends Seeder
{
	public function run()
	{
		$permissions = [
			'Admin' => null,
			// Student Permissions
//			'Manage Schedules' => null,
//			'Change Pricing' => null,
//			'Update Profile' => null,
//			'Book Space' => null,
//			'Take Payment' => null,
//			'Run Reports' => null,
//			// Instructor Permissions
//			'Make a Reservation' => null,
//			'Cancel Reservation' => null,
//			'Make a Payment' => null,
		];

		foreach ($permissions as $permission_name => $val){
			$permissions[$permission_name] = Permission::create(['name' => $permission_name]);
		}

		$adminRole = Role::create(['name' => 'Admin']);
		$role_permissions = ['Admin'];
		foreach ($role_permissions as $permission_name){
			$adminRole->givePermissionTo($permissions[$permission_name]);
		}

		$instructorRole = Role::create(['name' => 'Instructor']);
//		$role_permissions = [
//			'Manage Schedules',
//			'Book Space',
//			'Take Payment',
//		];
//		foreach ($role_permissions as $permission_name){
//			$instructorRole->givePermissionTo($permissions[$permission_name]);
//		}

		$studentRole = Role::create(['name' => 'Student']);
//		$role_permissions = [
//			'Manage Users',
//			'Manage Schedules',
//			'Change Pricing',
//			'Book Space',
//			'Take Payment',
//			'Run Reports'
//		];
//		foreach ($role_permissions as $permission_name){
//			$studentRole->givePermissionTo($permissions[$permission_name]);
//		}
	}
}