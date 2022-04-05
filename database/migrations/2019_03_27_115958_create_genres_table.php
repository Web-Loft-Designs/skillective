<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGenresTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('image', 1024);
            $table->boolean('is_featured');
            $table->timestamps();
            $table->softDeletes();
        });

		Schema::create('user_genre', function (Blueprint $table) {
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

			$table->integer('genre_id')->unsigned()->index();
			$table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
		});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_genres');
        Schema::drop('genres');
    }
}
