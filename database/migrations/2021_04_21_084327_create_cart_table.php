<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('instructor_id')->unsigned()->index();
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('student_id')->unsigned()->index();
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('lesson_id')->unsigned()->index();
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');

            $table->string('description');

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
        Schema::dropIfExists('cart');
    }
}
