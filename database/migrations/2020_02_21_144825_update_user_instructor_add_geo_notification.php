<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserInstructorAddGeoNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_instructor', function (Blueprint $table) {
            $table->boolean('geo_notifications_allowed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_instructor', function (Blueprint $table) {
			$table->dropColumn('geo_notifications_allowed');
        });
    }
}
