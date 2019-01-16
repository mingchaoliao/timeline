<?php

namespace App\Http\Controllers\Api;

use App\Timeline\Exceptions\DateAttributeNotFoundException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class DateAttributeController extends Controller
{
    private $dateAttributeRepository;

    public function __construct(EloquentDateAttributeRepository $dateAttributeRepository)
    {
        $this->dateAttributeRepository = $dateAttributeRepository;
    }

    public function getTypeahead()
    {
        $options = $this->dateAttributeRepository->getTypeahead();
        return response()->json($options);
    }

    public function get()
    {
        $collection = $this->dateAttributeRepository->getCollection();
        return response()->json($collection);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0',
            'value' => 'string'
        ]);
        $attribute = $this->dateAttributeRepository->update(
            Input::get('id'),
            Input::get('value')
        );
        return response()->json($attribute->toArray());
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0'
        ]);
        return response()->json($this->dateAttributeRepository->delete(Input::get('id')));
    }

    public function createDateAttribute(Request $request) {
        $this->validate(
            $request,
            [
                'value' => [
                    'required',
                    function($attribute, $value, $fail) {
                        if($this->dateAttributeRepository->doesValueExist($value)) {
                            return $fail('Duplicated date attribute');
                        }
                    },
                ]
            ],
            [
                'required' => 'Missing value'
            ]
        );

        $dateAttribute = $this->dateAttributeRepository->create(
            $request->get('value'),
            Auth::user()->getId()
        );

        return response()->json($dateAttribute);
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
            'required' => 'Parameter "values" is required'
        ]);

        $values = $request->get('values');

        $response = $this->dateAttributeRepository->bulkCreate($values, Auth::user()->getId());

        return response()->json($response);
    }
}
