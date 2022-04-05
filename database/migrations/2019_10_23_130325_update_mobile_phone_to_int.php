<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMobilePhoneToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
			$table->bigInteger('mobile_phone')->nullable()->change();
        });

		Schema::table('invitations', function (Blueprint $table) {
			$table->bigInteger('invited_mobile_phone')->nullable()->change();
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
			$table->string('mobile_phone')->nullable()->change();
        });

		Schema::table('invitations', function (Blueprint $table) {
			$table->string('mobile_phone')->nullable()->change();
		});
    }
}
