<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('start_date');
            $table->unsignedInteger('start_date_format_id');
            $table->unsignedInteger('start_date_attribute_id')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->unsignedInteger('end_date_format_id')->nullable();
            $table->unsignedInteger('end_date_attribute_id')->nullable();
            $table->text('content');
            $table->unsignedInteger('period_id')->nullable();
            $table->unsignedInteger('create_user_id');
            $table->unsignedInteger('update_user_id')->nullable();
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
        Schema::dropIfExists('events');
    }
}
