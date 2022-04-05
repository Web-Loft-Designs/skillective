<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateRangeToGeoLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_geo_locations', function (Blueprint $table) {
			$table->date('date_from')->nullable();
			$table->date('date_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_geo_locations', function (Blueprint $table) {
            $table->dropColumn('date_from');
            $table->dropColumn('date_to');
        });
    }
}
