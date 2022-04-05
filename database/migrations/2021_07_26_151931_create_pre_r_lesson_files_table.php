<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreRLessonFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_r_lesson_files', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pre_r_lesson_id')->unsigned()->index()->nullable();
            $table->foreign('pre_r_lesson_id')->references('id')->on('pre_r_lessons')->onDelete('cascade');

            $table->string('name')->nullable();

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
        Schema::dropIfExists('pre_r_lesson_files');
    }
}
