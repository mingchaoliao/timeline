<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Services\PeriodService;
use App\Timeline\Domain\ValueObjects\PeriodId;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    /**
     * @var PeriodService
     */
    private $periodService;

    /**
     * PeriodController constructor.
     * @param PeriodService $periodRepository
     */
    public function __construct(PeriodService $periodRepository)
    {
        $this->periodService = $periodRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function getTypeahead()
    {
        return response()->json($this->periodService->getTypeahead());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function getAll()
    {
        return response()->json($this->periodService->getAll());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     * @throws \Illuminate\Validation\ValidationException
     */
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

        $period = $this->periodService->create($request->get('value'));

        return response()->json($period);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(string $id, Request $request)
    {
        $this->validate($request, [
            'value' => 'required'
        ]);

        $period = $this->periodService->update(
            PeriodId::createFromString($id),
            $request->get('value')
        );

        return response()->json($period);
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function delete(string $id)
    {
        $isSuccess = $this->periodService->delete(PeriodId::createFromString($id));

        return response()->json($isSuccess);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function bulkCreate(Request $request)
    {
        $this->validate($request, [
            'values' => 'array'
        ]);

        $values = $request->get('values');

        $response = $this->periodService->bulkCreate($values);

        return response()->json($response);
    }
}
