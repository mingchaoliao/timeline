<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\Domain\Collections\CreateEventRequestCollection;
use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Domain\Requests\PageableRequest;
use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Domain\Requests\UpdateEventRequest;
use App\Timeline\Domain\Services\EventService;
use App\Timeline\Domain\ValueObjects\EventId;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * EventController constructor.
     * @param EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function get(Request $request)
    {
        $pagableRequest = PageableRequest::createFromArray($request->all());

        $events = $this->eventService->get($pagableRequest);

        $count = $events->getCount();

        return response()->json($events)->header('X-Total-Count', $count);
    }

    public function getById(string $id)
    {
        return response()->json($this->eventService->getById(EventId::createFromString($id)));
    }

    public function search(Request $request)
    {
        $searchRequest = SearchEventRequest::createFromArray($request->all());

        $result = $this->eventService->search($searchRequest);

        return response()->json($result)
            ->header('X-Total-Count', $result->getHits()->getCount());
    }

    public function create(Request $request)
    {
        $createEventRequest = CreateEventRequest::fromArray($request->all());

        $event = $this->eventService->create($createEventRequest);

        return response()->json($event);
    }

    public function bulkCreate(Request $request)
    {
        $createEventRequestCollection = CreateEventRequestCollection::fromArray($request->all());

        $event = $this->eventService->bulkCreate($createEventRequestCollection);

        return response()->json($event);
    }

    public function update(string $id, Request $request)
    {
        $updateRequest = UpdateEventRequest::fromArray($request->all());

        $event = $this->eventService->update(EventId::createFromString($id), $updateRequest);

        return response()->json($event);
    }

    public function delete(string $id)
    {
        return response()->json($this->eventService->delete(EventId::createFromString($id)));
    }
}
