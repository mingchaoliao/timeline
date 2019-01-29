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
        $request->validate([
            'content' => 'nullable',
            'startDate' => 'nullable|event_date',
            'startDateFrom' => 'nullable|date_format:Y-m-d',
            'startDateTo' => 'nullable|date_format:Y-m-d',
            'endDate' => 'nullable|event_date',
            'endDateFrom' => 'nullable|date_format:Y-m-d',
            'endDateTo' => 'nullable|date_format:Y-m-d',
            'period' => 'nullable|string',
            'catalogs' => 'nullable|string',
            'page' => 'nullable|integer|gt:0',
            'pageSize' => 'nullable|integer|gt:0'
        ]);

        $searchRequest = SearchEventRequest::createFromArray($request->all());

        $result = $this->eventService->search($searchRequest);

        return response()->json($result)
            ->header('X-Total-Count', $result->getHits()->getCount());
    }

    public function create(Request $request)
    {
        $request->validate([
            'startDate' => 'required|event_date',
            'startDateAttributeId' => 'nullable|integer|gt:0',
            'endDate' => 'nullable|event_date',
            'endDateAttributeId' => 'nullable|integer|gt:0',
            'periodId' => 'nullable|integer|gt:0',
            'catalogIds.*' => 'integer',
            'content' => 'required|string',
            'imageIds.*' => 'integer',
        ]);

        $createEventRequest = CreateEventRequest::fromArray($request->all());

        $event = $this->eventService->create($createEventRequest);

        return response()->json($event);
    }

    public function bulkCreate(Request $request)
    {
        $request->validate([
            '*.startDate' => 'required|event_date',
            '*.startDateAttributeId' => 'nullable|integer',
            '*.endDate' => 'nullable|event_date',
            '*.endDateAttributeId' => 'nullable|integer',
            '*.periodId' => 'nullable|integer',
            '*.catalogIds.*' => 'integer',
            '*.content' => 'required|string',
            '*.imageIds.*' => 'integer',
        ]);

        $createEventRequestCollection = CreateEventRequestCollection::fromArray($request->all());

        $event = $this->eventService->bulkCreate($createEventRequestCollection);

        return response()->json($event);
    }

    public function update(string $id, Request $request)
    {
        $request->validate([
            'startDate' => 'required|event_date',
            'startDateAttributeId' => 'nullable|integer|gt:0',
            'endDate' => 'nullable|event_date',
            'endDateAttributeId' => 'nullable|integer|gt:0',
            'periodId' => 'nullable|integer|gt:0',
            'catalogIds.*' => 'integer',
            'content' => 'required|string',
            'imageIds.*' => 'integer',
        ]);

        $updateRequest = UpdateEventRequest::fromArray($request->all());

        $event = $this->eventService->update(EventId::createFromString($id), $updateRequest);

        return response()->json($event);
    }

    public function delete(string $id)
    {
        return response()->json($this->eventService->delete(EventId::createFromString($id)));
    }
}
