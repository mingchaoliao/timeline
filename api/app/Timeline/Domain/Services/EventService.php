<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/17/19
 * Time: 11:22 AM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\CreateEventRequestCollection;
use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Repositories\EventRepository;
use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Domain\Requests\PageableRequest;
use App\Timeline\Domain\Requests\UpdateEventRequest;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Exceptions\TimelineException;

class EventService
{
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * EventService constructor.
     * @param EventRepository $eventRepository
     * @param UserService $userService
     */
    public function __construct(EventRepository $eventRepository, UserService $userService)
    {
        $this->eventRepository = $eventRepository;
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
            throw TimelineException::ofUnableToRetrieveEvents();
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

            return $this->eventRepository->create($request, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateEvent();
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

            return $this->eventRepository->bulkCreate($requests, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateEvent();
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

            return $this->eventRepository->update(
                $id,
                $request,
                $currentUser->getId()
            );
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdateEvent($id);
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

            return $this->eventRepository->delete($id);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToDeleteEvent($id);
        }
    }
}