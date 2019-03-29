<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Services\PeriodService;
use App\Timeline\Domain\ValueObjects\PeriodId;
use Carbon\Carbon;
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
     * @param ValidatorFactory $validatorFactory
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function create(Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'value' => 'required|string'
        ]);

        $period = $this->periodService->create($request->get('value'));

        return response()->json($period);
    }

    /**
     * @param string $id
     * @param Request $request
     * @param ValidatorFactory $validatorFactory
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function update(string $id, Request $request, ValidatorFactory $validatorFactory)
    {
        $params = $request->all();
        $params['id'] = $id;

        $validatorFactory->validate($params, [
            'id' => 'required|id',
            'value' => 'required|string',
            'startDate' => 'nullable|date_format:Y-m-d'
        ]);

        $period = $this->periodService->update(
            PeriodId::createFromString($id),
            $request->get('value'),
            $request->get('startDate') === null ? null : Carbon::createFromFormat('Y-m-d', $request->get('startDate'))
        );

        return response()->json($period);
    }

    /**
     * @param string $id
     * @param ValidatorFactory $validatorFactory
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function delete(string $id, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate(['id' => $id], [
            'id' => 'required|id'
        ]);

        $isSuccess = $this->periodService->delete(PeriodId::createFromString($id));

        return response()->json($isSuccess);
    }

    /**
     * @param Request $request
     * @param ValidatorFactory $validatorFactory
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function bulkCreate(Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'values' => 'array',
            'values.*' => 'string',
        ]);

        $values = $request->get('values') ?? [];

        $response = $this->periodService->bulkCreate($values);

        return response()->json($response);
    }
}
