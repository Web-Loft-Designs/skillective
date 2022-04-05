<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeesToPurchasedLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchased_lessons', function (Blueprint $table) {
            $table->float('service_fee')->nullable();
            $table->float('processor_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchased_lessons', function (Blueprint $table) {
            $table->dropColumn('service_fee');
            $table->dropColumn('processor_fee');
        });
    }
}
