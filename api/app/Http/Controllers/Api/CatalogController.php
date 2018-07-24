<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CatalogNotFoundException;
use App\Repositories\CatalogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    private $catalogRepository;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    /**
     * @OAS\Get(
     *     path="/api/catalog",
     *     tags={"catalog"},
     *     summary="Get all catalogs",
     *     description="Get all catalogs",
     *     operationId="catalog.get",
     *     @OAS\RequestBody(
     *         description="Create user object",
     *         required=true
     *     ),
     *     @OAS\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */
    public function get() {
        $collection = $this->catalogRepository->getCollection();
        return response()->json($collection);
    }

    /**
     * @OAS\Post(
     *     path="/api/catalog",
     *     tags={"catalog"},
     *     summary="Get all catalogs",
     *     description="Get all catalogs",
     *     operationId="catalog.post",
     *     @OAS\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */
    public function createCatalog(Request $request) {
        $this->validate(
            $request,
            [
                'value' => [
                    'required',
                    function($attribute, $value, $fail) {
                        if($this->catalogRepository->doesValueExist($value)) {
                            return $fail('Duplicated catalog');
                        }
                    },
                ]
            ],
            [
                'required' => 'Missing value'
            ]
        );

        $catalog = $this->catalogRepository->create(
            $request->get('value'),
            Auth::user()->getId()
        );

        return response()->json($catalog);
    }

    public function bulkCreate(Request $request) {
        $this->validate($request, [
            'values' => [
                'required',
                function($attribute, $value, $fail) {
                    if(!is_array($value)) {
                        $fail('Parameter "values" must be a list of string');
                        return;
                    }
                    foreach($value as $item) {
                        if(!is_string($item)) {
                            $fail('Parameter "values" must be a list of string');
                        }
                    }
                },
            ]
        ], [
            'required' => 'Values is required'
        ]);

        $values = $request->get('values');

        $response = $this->catalogRepository->bulkCreate($values, Auth::user()->getId());

        return response()->json($response);
    }
}
