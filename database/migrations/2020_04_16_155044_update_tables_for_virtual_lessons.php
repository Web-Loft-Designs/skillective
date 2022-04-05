<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesForVirtualLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('lesson_type')->default('in_person');
            $table->string('location')->nullable()->change();
            $table->string('room_sid')->nullable();
            $table->boolean('room_completed')->nullable();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('disconnected')->default(false);
        });

        Schema::create('room_chat_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('lesson_id')->unsigned()->nullable();
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->text('message');
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
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('lesson_type');
            $table->string('location')->nullable(false)->change();
            $table->dropColumn('room_sid');
            $table->dropColumn('room_completed');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('disconnected');
        });

        Schema::dropIfExists('room_chat_messages');
    }
}
