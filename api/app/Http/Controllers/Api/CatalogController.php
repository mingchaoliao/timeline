<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\Validators\ValidatorFactory;
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
     * @param ValidatorFactory $validatorFactory
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public function create(Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'value' => 'required|string'
        ]);

        $catalog = $this->catalogService->create($request->get('value'));

        return response()->json($catalog);
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

        $catalog = $this->catalogService->update(
            CatalogId::createFromString($id),
            $request->get('value')
        );

        return response()->json($catalog);
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

        $isSuccess = $this->catalogService->delete(CatalogId::createFromString($id));

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

        $response = $this->catalogService->bulkCreate($values);

        return response()->json($response);
    }
}
