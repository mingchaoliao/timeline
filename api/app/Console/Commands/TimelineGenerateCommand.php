<?php

namespace App\Console\Commands;

use App\Repositories\EventRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TimelineGenerateCommand extends Command
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timeline:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate timeline data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EventRepository $eventRepository)
    {
        parent::__construct();
        $this->eventRepository = $eventRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $events = $this->eventRepository->getCollection();

        $timelineConfig = $events->toTimelineArray();

        if(count($timelineConfig) === 0) {
            $timelineConfig = [
                [
                    'start_date' => [
                        'year' => Carbon::now()->year
                    ],
                    'unique_id' => 1,
                    'text' => ['text' => '<p>Sign in as administrator to create the first event!</p>']
                ]
            ];
        }

        $json = json_encode([
            'events' => $timelineConfig
        ]);

        File::put(Storage::path('public/timeline.json'), $json);
    }
}
