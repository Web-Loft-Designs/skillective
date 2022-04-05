<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreRLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_r_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('video');
            $table->string('preview');
            $table->text('description');
            $table->float('price');
			$table->integer('instructor_id')->unsigned()->index();
			$table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('genre_id')->unsigned()->index();
			$table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');

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
        Schema::dropIfExists('pre_r_lessons');
    }
}
