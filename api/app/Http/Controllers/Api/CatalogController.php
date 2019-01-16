<?php

namespace App\Http\Controllers\Api;

use App\Timeline\Exceptions\CatalogNotFoundException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class CatalogController extends Controller
{
    private $catalogRepository;

    public function __construct(EloquentCatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    public function getTypeahead()
    {
        $options = $this->catalogRepository->getTypeahead();
        return response()->json($options);
    }

    public function get()
    {
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

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0',
            'value' => 'string'
        ]);
        $catalog = $this->catalogRepository->update(
            Input::get('id'),
            Input::get('value')
        );
        return response()->json($catalog->toArray());
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0'
        ]);
        return response()->json($this->catalogRepository->delete(Input::get('id')));
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
