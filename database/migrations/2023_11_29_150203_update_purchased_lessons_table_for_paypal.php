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
        Schema::table('purchased_lessons', function (Blueprint $table) {
            $table->string('pp_processor_fee', )->nullable()->after('service_fee');
            $table->string('pp_reference_id', )->nullable()->after('transaction_id');
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
            $table->dropColumn(['pp_processor_fee','pp_reference_id']);

        });
    }
};
