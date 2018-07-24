<?php

use Illuminate\Database\Migrations\Migration;

class AddForeignKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('images', function(Blueprint $table) {
//            $table->foreign('event_id')
//                ->references('id')->on('events')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//        });
//
//        Schema::table('events', function(Blueprint $table) {
//            $table->foreign('start_date_attribute_id')
//                ->references('id')->on('date_attributes')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//            $table->foreign('end_date_attribute_id')
//                ->references('id')->on('date_attributes')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//            $table->foreign('period_id')
//                ->references('id')->on('periods')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//            $table->foreign('create_user_id')
//                ->references('id')->on('users')
//                ->onUpdate('cascade')
//                ->onDelete('set null');
//            $table->foreign('update_user_id')
//                ->references('id')->on('users')
//                ->onUpdate('cascade')
//                ->onDelete('set null');
//            $table->foreign('start_date_format_id')
//                ->references('id')->on('date_formats')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//            $table->foreign('end_date_format_id')
//                ->references('id')->on('date_formats')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//        });
//
//        Schema::table('catalog_event', function(Blueprint $table) {
//            $table->foreign('catalog_id')
//                ->references('id')->on('catalogs')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//            $table->foreign('event_id')
//                ->references('id')->on('events')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//        });
        if(Elasticsearch::indices()->exists([
            'index' => 'timelines'
        ])) {
            Elasticsearch::indices()->delete([
                'index' => 'timelines'
            ]);
        }

        Elasticsearch::indices()->create([
            'index' => 'timelines',
            'body' => [
                'mappings' => [
                    'event' => [
                        'properties' => [
                            'startDate' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'period' => [
                                'type' => 'long'
                            ],
                            'catalogs' => [
                                'type' => 'long'
                            ],
                            'content' => [
                                'type' => 'text',
                                'analyzer' => 'ik_max_word',
                                'search_analyzer' => 'ik_max_word'
                            ],
                        ]
                    ]
                ]
            ]
        ]);

        \Illuminate\Support\Facades\Artisan::call('passport:client', [
            '--password' => true,
            '--name' => 'WebPasswordGrantClient'
        ]);

        \Illuminate\Support\Facades\Artisan::call('timeline:generate');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
