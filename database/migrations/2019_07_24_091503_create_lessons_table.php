<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('instructor_id')->unsigned()->index();
			$table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('genre_id')->unsigned()->index();
			$table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
			$table->dateTime('start');
			$table->dateTime('end');
			$table->integer('spots_available');
			$table->float('spot_price');
			$table->string('location');
			$table->string('address');
			$table->string('city');
			$table->string('state');
			$table->string('zip');
			$table->boolean('is_cancelled')->default(0);


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
        Schema::dropIfExists('lessons');
    }
}
