<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasedLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchased_lessons', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pre_r_lesson_id')->unsigned()->index()->nullable();
            $table->foreign('pre_r_lesson_id')->references('id')->on('pre_r_lessons')->onDelete('cascade');

			$table->integer('student_id')->unsigned()->index();
			$table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('payment_method_type')->nullable();
			$table->string('payment_method_token')->nullable();

            $table->string('transaction_status')->nullable();
            $table->string('status')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_created_at')->nullable();
            $table->string('status_reason')->nullable();

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
        Schema::dropIfExists('purchased_lessons');
    }
}
