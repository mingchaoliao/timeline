<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Domain\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * @var EventService
     */
    private $eventService;

    public function get(Request $request)
    {

    }

    public function getById(string $id)
    {

    }

    public function search(Request $request)
    {

    }

    public function create(Request $request)
    {
        $createEventRequest = CreateEventRequest::fromArray($request->all());

        $event = $this->eventService->create($createEventRequest);

        return response()->json($event);
    }

    public function bulkCreate(Request $request)
    {

    }

    public function update(Request $request)
    {

    }

    public function delete(Request $request)
    {

    }
}
