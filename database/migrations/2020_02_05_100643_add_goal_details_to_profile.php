<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoalDetailsToProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('goal_value')->nullable();
			$table->string('goal_description')->nullable();
			$table->string('goal_color', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
			$table->dropColumn('goal_value');
			$table->dropColumn('goal_description');
			$table->dropColumn('goal_color');
        });
    }
}
