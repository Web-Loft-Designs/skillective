<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('invited_by')->unsigned()->index();
			$table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
			$table->string('invited_as_instructor')->boolean();
			$table->string('invited_name')->nullable();
			$table->string('invited_email')->nullable();
			$table->string('invited_mobile_phone')->nullable();
			$table->integer('invited_user_id')->nullable()->unsigned();
			$table->foreign('invited_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitations');
    }
}
