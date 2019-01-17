<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Services\CatalogService;
use App\Timeline\Domain\ValueObjects\CatalogId;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * @var CatalogService
     */
    private $catalogService;

    /**
     * CatalogController constructor.
     * @param CatalogService $catalogService
     */
    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function getTypeahead()
    {
        return response()->json($this->catalogService->getTypeahead());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function getAll()
    {
        return response()->json($this->catalogService->getAll());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createCatalog(Request $request)
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

        $catalog = $this->catalogService->create($request->get('value'));

        return response()->json($catalog);
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

        $catalog = $this->catalogService->update(
            new CatalogId($request->get('id')),
            $request->get('value')
        );

        return response()->json($catalog);
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

        $isSuccess = $this->catalogService->delete(new CatalogId($request->get('id')));

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

        $response = $this->catalogService->bulkCreate($values);

        return response()->json($response);
    }
}
