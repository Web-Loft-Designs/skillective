<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('content')->nullable();
            $table->integer('position');
            $table->timestamps();
        });

        Schema::create('faq_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->integer('position');
            $table->timestamps();
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->integer('faq_category_id')->unsigned()->index()->nullable();
            $table->foreign('faq_category_id')->references('id')->on('faq_categories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_categories');
    }
}
