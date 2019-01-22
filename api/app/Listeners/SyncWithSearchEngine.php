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

class SyncWithSearchEngine implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @param EventService $eventService
     * @return void
     */
    public function handle($event, EventService $eventService)
    {
        if ($event instanceof TimelineEventsCreated) {
            $eventService->bulkIndex($event->getEvents());
        } elseif ($event instanceof TimelineEventUpdated) {
            $eventService->index($event->getEvent());
        } elseif ($event instanceof TimelineEventCreated) {
            $eventService->index($event->getEvent());
        } elseif ($event instanceof TimelineEventDeleted) {
            $eventService->deleteDocument($event->getEventId());
        } elseif ($event instanceof TimelinePeriodUpdated) {
            $events = $eventService->getByPeriodId($event->getPeriodId());
            $eventService->bulkIndex($events);
        } elseif ($event instanceof TimelinePeriodDeleted) {
            $eventService->indexAll();
        } elseif ($event instanceof TimelineCatalogUpdated) {
            $events = $eventService->getByCatalogId($event->getCatalogId());
            $eventService->bulkIndex($events);
        } elseif ($event instanceof TimelineCatalogDeleted) {
            $eventService->indexAll();
        }
    }
}
