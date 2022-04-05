<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');

			$table->integer('lesson_id')->unsigned()->index();
			$table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');

			$table->integer('instructor_id')->unsigned()->index();
			$table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');

			$table->integer('student_id')->unsigned()->index();
			$table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

			$table->float('spot_price'); // price may be changed after booking created
			$table->string('special_request')->default('');
			$table->string('status');

            $table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
