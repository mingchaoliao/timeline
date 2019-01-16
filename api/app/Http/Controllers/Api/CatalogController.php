<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\UserId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    private $catalogRepository;

    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    public function getTypeahead()
    {
        return response()->json($this->catalogRepository->getTypeahead());
    }

    public function getAll()
    {
        return response()->json($this->catalogRepository->getAll());
    }

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

        $catalog = $this->catalogRepository->create(
            $request->get('value'),
            new UserId(Auth::user()->getId())
        );

        return response()->json($catalog);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0',
            'value' => 'required'
        ]);

        $catalog = $this->catalogRepository->update(
            new CatalogId($request->get('id')),
            $request->get('value'),
            new UserId(Auth::user()->getId())
        );

        return response()->json($catalog);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0'
        ]);

        $isSuccess = $this->catalogRepository->delete(new CatalogId($request->get('id')));

        return response()->json($isSuccess);
    }

    public function bulkCreate(Request $request)
    {
        $this->validate($request, [
            'values' => 'array'
        ]);

        $values = $request->get('values');

        $response = $this->catalogRepository->bulkCreate(
            $values,
            new UserId(Auth::user()->getId())
        );

        return response()->json($response);
    }
}
