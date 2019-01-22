<?php

namespace App\Listeners;


use App\Timeline\Domain\Services\TimelineService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateTimeline implements ShouldQueue
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
     * @param TimelineService $timelineService
     * @return void
     */
    public function handle($event, TimelineService $timelineService, Filesystem $fs)
    {
        $timelineData = $timelineService->getTimelineArray();
        $json = json_encode($timelineData);
        $fs->put('public/timeline.json', $json);
    }
}
