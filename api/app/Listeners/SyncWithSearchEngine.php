<?php

namespace App\Listeners;

use App\Events\TimelineCatalogDeleted;
use App\Events\TimelineCatalogUpdated;
use App\Events\TimelineEventCreated;
use App\Events\TimelineEventDeleted;
use App\Events\TimelineEventsCreated;
use App\Events\TimelineEventUpdated;
use App\Events\TimelinePeriodDeleted;
use App\Events\TimelinePeriodUpdated;
use App\Timeline\Domain\Services\EventService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SyncWithSearchEngine implements ShouldQueue
{
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * Create the event listener.
     *
     * @param EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        if ($event instanceof TimelineEventsCreated) {
            $this->eventService->bulkIndex($event->getEvents());
        } elseif ($event instanceof TimelineEventUpdated) {
            $this->eventService->index($event->getEvent());
        } elseif ($event instanceof TimelineEventCreated) {
            $this->eventService->index($event->getEvent());
        } elseif ($event instanceof TimelineEventDeleted) {
            $this->eventService->deleteDocument($event->getEventId());
        } elseif ($event instanceof TimelinePeriodUpdated) {
            $events = $this->eventService->getByPeriodId($event->getPeriodId());
            $this->eventService->bulkIndex($events);
        } elseif ($event instanceof TimelinePeriodDeleted) {
            $this->eventService->indexAll();
        } elseif ($event instanceof TimelineCatalogUpdated) {
            $events = $this->eventService->getByCatalogId($event->getCatalogId());
            $this->eventService->bulkIndex($events);
        } elseif ($event instanceof TimelineCatalogDeleted) {
            $this->eventService->indexAll();
        }
    }
}
