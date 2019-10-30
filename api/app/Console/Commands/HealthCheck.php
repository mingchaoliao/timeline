<?php

namespace App\Console\Commands;

use App\Timeline\App\HealthCheckService;

class HealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:health {--i|ignore=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check whether the application is healthy';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param HealthCheckService $service
     * @return mixed
     */
    public function handle(HealthCheckService $service)
    {
        $ignores = $this->option('ignore');
        if ($ignores === null) {
            $ignores = [];
        } else {
            $ignores = explode(',', $ignores);
        }

        $healthy = true;
        $status = [];

        if (!in_array('is_db_connected', $ignores)) {
            $s = $service->isDBConnected();
            $status['is_db_connected'] = $s;
            $healthy &= $s;
        }

        if (!in_array('is_db_setup_completed', $ignores)) {
            $s = $service->isDBSetupCompleted();
            $status['is_db_setup_completed'] = $s;
            $healthy &= $s;
        }

        if (!in_array('is_queue_connected', $ignores)) {
            $s = $service->isQueueConnected();
            $status['is_queue_connected'] = $s;
            $healthy &= $s;
        }

        if (!in_array('is_search_engine_connected', $ignores)) {
            $s = $service->isSearchEngineConnected();
            $status['is_search_engine_connected'] = $s;
            $healthy &= $s;
        }

        if (!in_array('is_search_engine_index_created', $ignores)) {
            $s = $service->isSearchEngineIndexCreated();
            $status['is_search_engine_index_created'] = $s;
            $healthy &= $s;
        }

        $this->line(json_encode($status));

        return $healthy ? 0 : 1;
    }
}
