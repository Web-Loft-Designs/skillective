<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profile;

class SetupProfileLatLngField extends Command
{
    protected $signature = 'setup:profile_coordinates';

    protected $description = "get lat/lng from location and save into separate fields";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Profile::withTrashed()->get()->each(function($profile){
            $profile->save();
		});
    }
}
