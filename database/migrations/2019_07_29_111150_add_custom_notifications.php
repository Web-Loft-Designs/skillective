<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag');
            $table->text('data')->nullable();
            $table->timestamps();
        });
		
		Schema::create('custom_notification_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('method');
            $table->text('content');
            $table->text('data')->nullable();
			$table->boolean('active')->default(1);

			$table->unsignedInteger('custom_notification_id')->index();
            $table->foreign('custom_notification_id')->references('id')->on('custom_notifications')
                ->onDelete('cascade');
            $table->timestamps();
        });
		
		Schema::table('profiles', function (Blueprint $table) {
            $table->string('notification_methods')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('notification_methods');
        });
		Schema::dropIfExists('custom_notification_methods');
        Schema::dropIfExists('custom_notifications');
    }
}
