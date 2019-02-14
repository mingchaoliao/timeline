<?php

namespace App\Listeners;


use App\Timeline\Domain\Services\TimelineService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateTimeline implements ShouldQueue
{
    /**
     * @var TimelineService
     */
    private $timelineService;
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * Create the event listener.
     *
     * @param TimelineService $timelineService
     * @param Filesystem $fs
     */
    public function __construct(TimelineService $timelineService, Filesystem $fs)
    {
        $this->timelineService = $timelineService;
        $this->fs = $fs;
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $timelineData = $this->timelineService->getTimelineArray();
        $json = json_encode($timelineData);
        $this->fs->put('public/timeline.json', $json);
    }
}
