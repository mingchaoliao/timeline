<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/17/19
 * Time: 11:22 AM
 */

namespace App\Timeline\Domain\Services;


use App\Events\TimelineEventCreated;
use App\Events\TimelineEventDeleted;
use App\Events\TimelineEventsCreated;
use App\Events\TimelineEventUpdated;
use App\Timeline\Domain\Collections\CreateEventRequestCollection;
use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Models\EventSearchResult;
use App\Timeline\Domain\Repositories\EventRepository;
use App\Timeline\Domain\Repositories\SearchEventRepository;
use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Domain\Requests\PageableRequest;
use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Domain\Requests\UpdateEventRequest;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Exceptions\TimelineException;
use Illuminate\Support\Facades\Log;

class EventService
{
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var SearchEventRepository
     */
    private $searchRepository;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * EventService constructor.
     * @param EventRepository $eventRepository
     * @param SearchEventRepository $searchRepository
     * @param UserService $userService
     */
    public function __construct(EventRepository $eventRepository, SearchEventRepository $searchRepository, UserService $userService)
    {
        $this->eventRepository = $eventRepository;
        $this->searchRepository = $searchRepository;
        $this->userService = $userService;
    }

    /**
     * @param EventId $id
     * @return Event
     * @throws TimelineException
     */
    public function getById(EventId $id): Event
    {
        try {
            return $this->eventRepository->getById($id);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveEventById($id);
        }
    }

    public function getByPeriodId(PeriodId $id): EventCollection
    {
        return $this->eventRepository->getByPeriodId($id);
    }

    public function getByCatalogId(CatalogId $id): EventCollection
    {
        return $this->eventRepository->getByCatalogId($id);
    }

    /**
     * @param SearchEventRequest $request
     * @return EventSearchResult
     * @throws TimelineException
     */
    public function search(SearchEventRequest $request): EventSearchResult
    {
        try {
            $result = $this->searchRepository->search($request);

            return $result;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToSearchEvents($e);
        }
    }

    /**
     * @param EventIdCollection $ids
     * @return EventCollection
     * @throws TimelineException
     */
    public function getByIds(EventIdCollection $ids): EventCollection
    {
        try {
            return $this->eventRepository->getByIds($ids);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveEvents();
        }
    }

    /**
     * @param PageableRequest $request
     * @return EventCollection
     * @throws TimelineException
     */
    public function get(PageableRequest $request): EventCollection
    {
        try {
            return $this->eventRepository->get($request);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveEvents($e);
        }
    }

    /**
     * @param CreateEventRequest $request
     * @return Event
     * @throws TimelineException
     */
    public function create(CreateEventRequest $request): Event
    {
        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreateEvent();
            }

            $event = $this->eventRepository->create($request, $currentUser->getId());

            TimelineEventCreated::dispatch($event);

            return $event;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateEvent($e);
        }
    }

    /**
     * @param CreateEventRequestCollection $requests
     * @return EventCollection
     * @throws TimelineException
     */
    public function bulkCreate(CreateEventRequestCollection $requests): EventCollection
    {
        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreateEvent();
            }

            $events = $this->eventRepository->bulkCreate($requests, $currentUser->getId());

            TimelineEventsCreated::dispatch($events);

            return $events;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateEvent($e);
        }
    }

    /**
     * @param EventId $id
     * @param UpdateEventRequest $request
     * @return Event
     * @throws TimelineException
     */
    public function update(EventId $id, UpdateEventRequest $request): Event
    {
        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToUpdateEvent($id);
            }

            $event = $this->eventRepository->update(
                $id,
                $request,
                $currentUser->getId()
            );

            TimelineEventUpdated::dispatch($event);

            return $event;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdateEvent($id, $e);
        }
    }

    /**
     * @param EventId $id
     * @return bool
     * @throws TimelineException
     */
    public function delete(EventId $id): bool
    {
        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToDeleteEvent($id);
            }

            $success = $this->eventRepository->delete($id);

            TimelineEventDeleted::dispatch($id);

            return $success;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToDeleteEvent($id, $e);
        }
    }

    public function index(Event $event): void
    {
        $this->searchRepository->index($event);
    }

    public function indexAll(): void
    {
        $events = $this->eventRepository->getAll();
        $this->bulkIndex($events);
    }

    public function bulkIndex(EventCollection $events): void
    {
        $this->searchRepository->bulkIndex($events);
    }

    public function deleteDocument(EventId $eventId): void
    {
        $this->searchRepository->deleteDocument($eventId);
    }
}