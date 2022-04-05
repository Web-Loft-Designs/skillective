<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenreCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_categories', function (Blueprint $table) {
            $table->increments('id');
			$table->string('title', 255);
            $table->timestamps();
        });

		Schema::table('genres', function (Blueprint $table) {
			$table->integer('genre_category_id')->unsigned()->index()->nullable();
			$table->foreign('genre_category_id')->references('id')->on('genre_categories')->onDelete('cascade');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('genres', function (Blueprint $table) {
			$table->dropColumn('genre_category_id');
		});
        Schema::dropIfExists('genre_categories');
    }
}
