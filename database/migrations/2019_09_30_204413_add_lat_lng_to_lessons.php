<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatLngToLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
			$table->float('lat', 11, 6)->nullable();
			$table->float('lng', 11, 6)->nullable();
        });

		Schema::table('user_geo_locations', function (Blueprint $table) {
			$table->float('lat', 11, 6)->nullable();
			$table->float('lng', 11, 6)->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
			$table->dropColumn('lat');
			$table->dropColumn('lng');
        });

		Schema::table('user_geo_locations', function (Blueprint $table) {
			$table->dropColumn('lat');
			$table->dropColumn('lng');
		});
    }
}
