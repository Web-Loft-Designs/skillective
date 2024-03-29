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
        Schema::table('users', function (Blueprint $table) {
            $table->string('pp_tracking_id', )->nullable()->after('finish_registration_token');
            $table->string('pp_merchant_id', )->nullable()->after('pp_tracking_id');
            $table->string('pp_referral_id', )->nullable()->after('pp_merchant_id');
            $table->string('pp_account_status', )->nullable()->after('pp_referral_id');
            $table->string('pp_customer_id', )->nullable()->after('pp_account_status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pp_merchant_id','pp_tracking_id', 'pp_referral_id', 'pp_account_status','pp_customer_id']);

        });
    }
};
