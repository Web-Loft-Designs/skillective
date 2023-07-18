<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regular_notification', function (Blueprint $table) {
            $table->dateTime('date_send_time_utc')->after('sended_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regular_notification', function (Blueprint $table) {
            $table->dropColumn('date_send_time_utc');
        });
    }
};
