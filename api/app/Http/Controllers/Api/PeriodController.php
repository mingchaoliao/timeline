<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodController extends Controller
{
    private $periodRepository;

    public function __construct(PeriodRepository $periodRepository)
    {
        $this->periodRepository = $periodRepository;
    }

    public function getTypeahead()
    {
        return response()->json($this->periodRepository->getTypeahead());
    }

    public function getAll()
    {
        return response()->json($this->periodRepository->getAll());
    }

    public function createPeriod(Request $request)
    {
        $this->validate(
            $request,
            [
                'value' => 'required'
            ],
            [
                'required' => 'Missing value'
            ]
        );

        $period = $this->periodRepository->create(
            $request->get('value'),
            new UserId(Auth::user()->getId())
        );

        return response()->json($period);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0',
            'value' => 'required'
        ]);

        $period = $this->periodRepository->update(
            new PeriodId($request->get('id')),
            $request->get('value'),
            new UserId(Auth::user()->getId())
        );

        return response()->json($period);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0'
        ]);

        $isSuccess = $this->periodRepository->delete(new PeriodId($request->get('id')));

        return response()->json($isSuccess);
    }

    public function bulkCreate(Request $request)
    {
        $this->validate($request, [
            'values' => 'array'
        ]);

        $values = $request->get('values');

        $response = $this->periodRepository->bulkCreate(
            $values,
            new UserId(Auth::user()->getId())
        );

        return response()->json($response);
    }
}
