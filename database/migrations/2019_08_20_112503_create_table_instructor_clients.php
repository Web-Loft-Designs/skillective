<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInstructorClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('instructor_client', function (Blueprint $table) {
			$table->integer('instructor_id')->unsigned()->index();
			$table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');

			$table->integer('client_id')->unsigned()->index();
			$table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('instructor_client');
    }
}
