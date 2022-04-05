<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentInstructor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('student_instructor', function (Blueprint $table) {
			$table->integer('student_id')->unsigned()->index();
			$table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

			$table->integer('instructor_id')->unsigned()->index();
			$table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');

			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_instructor');
    }
}
