<?php

use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateFormat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDateFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('date_formats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mysql_format')->unique();
            $table->string('php_format')->unique();
            $table->tinyInteger('has_year');
            $table->tinyInteger('has_month');
            $table->tinyInteger('has_day');
            $table->tinyInteger('is_attribute_allowed')->default(0);
            $table->timestamps();
        });

        EloquentDateFormat::createNew('YYYY', 'Y', true, false, false, true);
        EloquentDateFormat::createNew('YYYY-MM', 'Y-m', true, true, false);
        EloquentDateFormat::createNew('YYYY-MM-DD', 'Y-m-d', true, true, true);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('date_formats');
    }
}
