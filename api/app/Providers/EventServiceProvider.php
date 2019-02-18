<?php

namespace App\Providers;

use App\Events\TimelineCatalogDeleted;
use App\Events\TimelineCatalogUpdated;
use App\Events\TimelineEventCreated;
use App\Events\TimelineEventDeleted;
use App\Events\TimelineEventsCreated;
use App\Events\TimelineEventUpdated;
use App\Events\TimelinePeriodDeleted;
use App\Events\TimelinePeriodUpdated;
use App\Listeners\GenerateTimeline;
use App\Listeners\SyncWithSearchEngine;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],
        TimelineCatalogDeleted::class => [
            SyncWithSearchEngine::class
        ],
        TimelineCatalogUpdated::class => [
            SyncWithSearchEngine::class
        ],
        TimelineEventCreated::class => [
            GenerateTimeline::class,
            SyncWithSearchEngine::class
        ],
        TimelineEventDeleted::class => [
            GenerateTimeline::class,
            SyncWithSearchEngine::class
        ],
        TimelineEventsCreated::class => [
            GenerateTimeline::class,
            SyncWithSearchEngine::class
        ],
        TimelineEventUpdated::class => [
            GenerateTimeline::class,
            SyncWithSearchEngine::class
        ],
        TimelinePeriodDeleted::class => [
            GenerateTimeline::class,
            SyncWithSearchEngine::class
        ],
        TimelinePeriodUpdated::class => [
            GenerateTimeline::class,
            SyncWithSearchEngine::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
