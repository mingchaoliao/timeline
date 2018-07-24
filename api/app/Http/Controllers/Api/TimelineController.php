<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class TimelineController extends Controller
{
    public function create()
    {
        Artisan::call('timeline:generate');

        return response()->json(true);
    }
}
