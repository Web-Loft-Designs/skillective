<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('instructor_id')->unsigned()->index();
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('student_id')->unsigned()->index();
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('genre_id')->unsigned()->index();
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
            $table->integer('lesson_id')->unsigned()->nullable();
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->integer('count_participants');
            $table->float('lesson_price');
            $table->string('lesson_type')->default('in_person');
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('status');
            $table->float('lat', 11, 6)->nullable();
            $table->float('lng', 11, 6)->nullable();
            $table->text('student_note')->nullable();
            $table->text('instructor_note')->nullable();
            $table->string('timezone_id');
            $table->string('timezone_offset_gmt')->nullable();

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
        Schema::dropIfExists('lesson_requests');
    }
}
