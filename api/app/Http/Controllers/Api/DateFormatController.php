<?php

namespace App\Http\Controllers\Api;

use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateFormatRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DateFormatController extends Controller
{
    private $dateFormatRepository;

    public function __construct(EloquentDateFormatRepository $dateFormatRepository)
    {
        $this->dateFormatRepository = $dateFormatRepository;
    }

    public function get() {
        $collection = $this->dateFormatRepository->getCollection();
        return response()->json($collection);
    }
}
