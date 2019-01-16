<?php

use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateAttribute;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('date_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
            $table->unsignedInteger('create_user_id');
            $table->unsignedInteger('update_user_id');
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
        Schema::dropIfExists('date_attributes');
    }
}
