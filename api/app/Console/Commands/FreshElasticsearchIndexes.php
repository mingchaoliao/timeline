<?php

namespace App\Console\Commands;

use Elasticsearch;
use Illuminate\Console\Command;

class FreshElasticsearchIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Elasticsearch::indices()->exists([
            'index' => 'timelines'
        ])) {
            Elasticsearch::indices()->delete([
                'index' => 'timelines'
            ]);

            $this->info('Index dropped: timelines');
        }

        $response = Elasticsearch::indices()->create([
            'index' => 'timelines',
            'body' => [
                'mappings' => [
                    'event' => [
                        'properties' => [
                            'id' => [
                                'type' => 'long',
                            ],
                            'startDateFrom' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'startDateTo' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'startDateStr' => [
                                'type' => 'keyword',
                            ],
                            'startDateAttribute' => [
                                'type' => 'keyword',
                            ],
                            'endDateFrom' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'endDateTo' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'endDateStr' => [
                                'type' => 'keyword',
                            ],
                            'endDateAttribute' => [
                                'type' => 'keyword',
                            ],
                            'period' => [
                                'type' => 'keyword'
                            ],
                            'catalogs' => [
                                'type' => 'keyword'
                            ],
                            'content' => [
                                'type' => 'text',
                                'analyzer' => 'ik_max_word',
                                'search_analyzer' => 'ik_smart'
                            ],
                        ]
                    ]
                ]
            ]
        ]);

        $this->info(sprintf(
            'Index created: %s',
            $response['index']
        ));

        return 0;
    }
}
