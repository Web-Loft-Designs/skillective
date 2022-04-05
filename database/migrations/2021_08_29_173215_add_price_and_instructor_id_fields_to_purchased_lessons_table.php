<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceAndInstructorIdFieldsToPurchasedLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchased_lessons', function (Blueprint $table) {
            $table->integer('instructor_id')->unsigned()->nullable();
			$table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');

            $table->float('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchased_lessons', function (Blueprint $table) {
            $table->dropColumn('instructor_id');
            $table->dropColumn('price');
        });
    }
}
