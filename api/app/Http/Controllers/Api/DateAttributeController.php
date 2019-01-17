<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createDateAttribute(Request $request)
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

        $dateAttribute = $this->dateAttributeService->create($request->get('value'));

        return response()->json($dateAttribute);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0',
            'value' => 'required'
        ]);

        $dateAttribute = $this->dateAttributeService->update(
            new DateAttributeId($request->get('id')),
            $request->get('value')
        );

        return response()->json($dateAttribute);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0'
        ]);

        $isSuccess = $this->dateAttributeService->delete(new DateAttributeId($request->get('id')));

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

        $response = $this->dateAttributeService->bulkCreate($values);

        return response()->json($response);
    }
}
