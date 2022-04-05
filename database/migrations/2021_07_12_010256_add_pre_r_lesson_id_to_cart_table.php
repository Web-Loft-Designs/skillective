<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreRLessonIdToCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            //
            
            $table->integer('lesson_id')->unsigned()->nullable()->change(50);

            $table->integer('pre_r_lesson_id')->unsigned()->index()->nullable();
            $table->foreign('pre_r_lesson_id')->references('id')->on('pre_r_lessons')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropForeign('cart_pre_r_lesson_id_foreign');
            $table->dropColumn('pre_r_lesson_id');
        });
    }
}
