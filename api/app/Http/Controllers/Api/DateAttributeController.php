<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DateAttributeNotFoundException;
use App\Repositories\DateAttributeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DateAttributeController extends Controller
{
    private $dateAttributeRepository;

    public function __construct(DateAttributeRepository $dateAttributeRepository)
    {
        $this->dateAttributeRepository = $dateAttributeRepository;
    }

    public function get() {
        $collection = $this->dateAttributeRepository->getCollection();
        return response()->json($collection);
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
