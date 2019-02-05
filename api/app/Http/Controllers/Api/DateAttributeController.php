<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Services\DateAttributeService;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use Illuminate\Http\Request;

class DateAttributeController extends Controller
{
    /**
     * @var DateAttributeService
     */
    private $dateAttributeService;

    /**
     * DateAttributeController constructor.
     * @param DateAttributeService $dateAttributeService
     */
    public function __construct(DateAttributeService $dateAttributeService)
    {
        $this->dateAttributeService = $dateAttributeService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function getTypeahead()
    {
        return response()->json($this->dateAttributeService->getTypeahead());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function getAll()
    {
        return response()->json($this->dateAttributeService->getAll());
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

        $dateAttribute = $this->dateAttributeService->create($request->get('value'));

        return response()->json($dateAttribute);
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
            'value' => 'required|string'
        ]);

        $dateAttribute = $this->dateAttributeService->update(
            DateAttributeId::createFromString($id),
            $request->get('value')
        );

        return response()->json($dateAttribute);
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

        $isSuccess = $this->dateAttributeService->delete(DateAttributeId::createFromString($id));

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
            'values' => 'required|array|filled',
            'values.*' => 'string',
        ]);

        $values = $request->get('values');

        $response = $this->dateAttributeService->bulkCreate($values);

        return response()->json($response);
    }
}
