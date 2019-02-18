<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 4:13 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Repositories\EventRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;

class TimelineService
{
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * TimelineService constructor.
     * @param EventRepository $eventRepository
     * @param Filesystem $fs
     */
    public function __construct(EventRepository $eventRepository, Filesystem $fs)
    {
        $this->eventRepository = $eventRepository;
        $this->fs = $fs;
    }

    public function generateTimeline(): void
    {
        $timelineData = json_encode($this->getTimelineArray());
        $this->fs->put('public/timeline.json', $timelineData);
    }

    public function getTimelineArray(): array
    {
        $timelineData = [];

        $events = $this->eventRepository->getAll();

        if (count($events) !== 0) {
            $timelineData['events'] = $events->map(function (Event $event) {
                return $event->toTimelineArray();
            })->toArray();
        } else {
            $timelineData['events'] = [
                [
                    'start_date' => [
                        'year' => Carbon::now()->year
                    ],
                    'unique_id' => '0',
                    'text' => ['text' => '<p>Sign in as administrator to create the first event!</p>']
                ]
            ];
        }

        return $timelineData;
    }
}