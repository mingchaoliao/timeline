<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Services\TimelineService;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentEventRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TimelineGenerateCommand extends Command
{
    /**
     * @var TimelineService
     */
    private $timelineService;

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
     * @param TimelineService $timelineService
     */
    public function __construct(TimelineService $timelineService)
    {
        parent::__construct();
        $this->timelineService = $timelineService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $timelineData = json_encode($this->timelineService->getTimelineArray());

        File::put(Storage::path('public/timeline.json'), $timelineData);
    }
}
