<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\UserId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DateAttributeController extends Controller
{
    private $dateAttributeRepository;

    public function __construct(DateAttributeRepository $dateAttributeRepository)
    {
        $this->dateAttributeRepository = $dateAttributeRepository;
    }

    public function getTypeahead()
    {
        return response()->json($this->dateAttributeRepository->getTypeahead());
    }

    public function getAll()
    {
        return response()->json($this->dateAttributeRepository->getAll());
    }

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

        $dateAttribute = $this->dateAttributeRepository->create(
            $request->get('value'),
            new UserId(Auth::user()->getId())
        );

        return response()->json($dateAttribute);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0',
            'value' => 'required'
        ]);

        $dateAttribute = $this->dateAttributeRepository->update(
            new DateAttributeId($request->get('id')),
            $request->get('value'),
            new UserId(Auth::user()->getId())
        );

        return response()->json($dateAttribute);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0'
        ]);

        $isSuccess = $this->dateAttributeRepository->delete(new DateAttributeId($request->get('id')));

        return response()->json($isSuccess);
    }

    public function bulkCreate(Request $request)
    {
        $this->validate($request, [
            'values' => 'array'
        ]);

        $values = $request->get('values');

        $response = $this->dateAttributeRepository->bulkCreate(
            $values,
            new UserId(Auth::user()->getId())
        );

        return response()->json($response);
    }
}
