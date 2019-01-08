<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\PeriodRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class PeriodController extends Controller
{
    private $periodRepository;

    public function __construct(PeriodRepository $periodRepository)
    {
        $this->periodRepository = $periodRepository;
    }

    public function getTypeahead()
    {
        $options = $this->periodRepository->getTypeahead();
        return response()->json($options);
    }

    public function get()
    {
        $collection = $this->periodRepository->getCollection();
        return response()->json($collection);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0',
            'value' => 'string'
        ]);
        $period = $this->periodRepository->update(
            Input::get('id'),
            Input::get('value')
        );
        return response()->json($period->toArray());
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'integer|gt:0'
        ]);
        return response()->json($this->periodRepository->delete(Input::get('id')));
    }

    public function createPeriod(Request $request)
    {
        $this->validate(
            $request,
            [
                'value' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($this->periodRepository->doesValueExist($value)) {
                            return $fail('Duplicated period');
                        }
                    },
                ]
            ],
            [
                'required' => 'Missing value'
            ]
        );

        $period = $this->periodRepository->create(
            $request->get('value'),
            Auth::user()->getId()
        );

        return response()->json($period);
    }

    public function bulkCreate(Request $request)
    {
        $this->validate($request, [
            'values' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_array($value)) {
                        $fail('Parameter "values" must be a list of string');
                        return;
                    }
                    foreach ($value as $item) {
                        if (!is_string($item)) {
                            $fail('Parameter "values" must be a list of string');
                        }
                    }
                },
            ]
        ], [
            'required' => 'Parameter "values" is required'
        ]);

        $values = $request->get('values');

        $response = $this->periodRepository->bulkCreate($values, Auth::user()->getId());

        return response()->json($response);
    }
}
