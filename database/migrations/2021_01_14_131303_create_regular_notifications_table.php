<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegularNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regular_notification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


            $table->integer('booking_id')->unsigned()->index();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');

            $table->enum('type', ['hourly', 'daily']);
            $table->enum('status', ['success', 'failed']);
            $table->string('message');

            $table->date('sended_at');
            
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
        Schema::dropIfExists('regular_notification');
    }
}
