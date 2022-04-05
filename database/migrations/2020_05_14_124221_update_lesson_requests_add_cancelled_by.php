<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLessonRequestsAddCancelledBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_requests', function (Blueprint $table) {
            $table->unsignedInteger('cancelled_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_requests', function (Blueprint $table) {
            $table->dropColumn('cancelled_by');
        });
    }
}
