<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\Validators\ValidatorFactory;
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
        $pagableRequest = PageableRequest::createFromValueArray($request->all());

        $events = $this->eventService->get($pagableRequest);

        $count = $events->getTotalCount();

        return response()->json($events)->header('X-Total-Count', $count);
    }

    public function getById(string $id, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate(['id' => $id], [
            'id' => 'required|id'
        ]);

        return response()->json($this->eventService->getById(new EventId(intval($id))));
    }

    public function search(Request $request)
    {
        $searchRequest = SearchEventRequest::createFromValueArray($request->all());

        $result = $this->eventService->search($searchRequest);

        return response()->json($result)
            ->header('X-Total-Count', $result->getHits()->getTotalCount());
    }

    public function create(Request $request)
    {
        $createEventRequest = CreateEventRequest::createFromValueArray($request->all());

        $event = $this->eventService->create($createEventRequest);

        return response()->json($event);
    }

    public function bulkCreate(Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'events' => 'array'
        ]);

        $createEventRequestCollection = CreateEventRequestCollection::createFromValueArray($request->all()['events'] ?? []);

        $event = $this->eventService->bulkCreate($createEventRequestCollection);

        return response()->json($event);
    }

    public function update(string $id, Request $request)
    {
        $params = $request->all();
        $params['id'] = $id;

        $updateRequest = UpdateEventRequest::createFromValueArray($params);

        $event = $this->eventService->update(new EventId(intval($id)), $updateRequest);

        return response()->json($event);
    }

    public function delete(string $id, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate(['id' => $id], [
            'id' => 'required|id'
        ]);

        return response()->json($this->eventService->delete(new EventId(intval($id))));
    }
}
