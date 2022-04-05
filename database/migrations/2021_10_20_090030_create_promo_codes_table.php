<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('instructor_id')->unsigned()->index();
			$table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('name');
            $table->string('lesson_type');
            $table->string('discount_type');
            $table->integer('discount');
            $table->date('start');
            $table->date('finish');
            $table->integer('users_count');
            $table->integer('used_time');
            $table->boolean('used_with_other_discounts');

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
        Schema::dropIfExists('promo_codes');
    }
}
