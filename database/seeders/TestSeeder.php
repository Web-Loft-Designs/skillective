<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestSeeder extends Seeder
{
	public function run()
    {
        $users = User::all();

        foreach($users as $user) {

            $user->update([
                'password' => Hash::make('admin123')
            ]);

        }




	}

}
