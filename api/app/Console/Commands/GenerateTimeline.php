<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Services\TimelineService;
use Illuminate\Support\Facades\Log;

class GenerateTimeline extends Command
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
        try {
            $this->timelineService->generateTimeline();
            $this->info('Timeline data file generated');
        } catch (\Exception $e) {
            $message = sprintf(
                'failed to generate timeline. Reason: %s',
                $e->getMessage()
            );
            $this->error($message);
            Log::error($message);
            return 1;
        }

        return 0;
    }
}
