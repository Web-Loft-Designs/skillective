<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBraintreeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bt_submerchant_id')->nullable();
            $table->string('bt_submerchant_status')->nullable();
            $table->string('bt_submerchant_status_reason')->nullable();
//            $table->string('bt_submerchant_signature')->nullable();
//            $table->string('bt_submerchant_payload')->nullable();
        });

		Schema::table('bookings', function (Blueprint $table) {
			$table->string('service_fee')->nullable();
			$table->string('transaction_status')->nullable();
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
			$table->dropColumn('bt_submerchant_id');
			$table->dropColumn('bt_submerchant_status');
			$table->dropColumn('bt_submerchant_status_reason');
//			$table->dropColumn('bt_submerchant_signature');
//			$table->dropColumn('bt_submerchant_payload');
        });

		Schema::table('bookings', function (Blueprint $table) {
			$table->dropColumn('service_fee');
			$table->dropColumn('transaction_status');
		});
    }
}
