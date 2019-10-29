<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\HealthCheckService;

class UtilController extends Controller
{
    public function healthCheck(HealthCheckService $service)
    {
        return response()->json([
            'is_db_connected' => $service->isDBConnected(),
            'is_db_setup_completed' => $service->isDBSetupCompleted(),
            'is_queue_connected' => $service->isQueueConnected(),
            'is_search_engine_connected' => $service->isSearchEngineConnected(),
            'is_search_engine_index_created' => $service->isSearchEngineIndexCreated()
        ]);
    }
}
