<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Repositories\DateFormatRepository;

class DateFormatController extends Controller
{
    private $dateFormatRepository;

    public function __construct(DateFormatRepository $dateFormatRepository)
    {
        $this->dateFormatRepository = $dateFormatRepository;
    }

    public function getAll()
    {
        return response()->json($this->dateFormatRepository->getAll());
    }
}
