<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Repositories\EventRepository;
use App\Timeline\Domain\Repositories\SearchEventRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class IndexAllEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '(Re)-index all events';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param EventRepository $eventRepository
     * @param SearchEventRepository $searchEventRepository
     * @return mixed
     */
    public function handle(EventRepository $eventRepository, SearchEventRepository $searchEventRepository)
    {
        try {
            $events = $eventRepository->getAll();
            $searchEventRepository->bulkIndex($events);
            $this->info(sprintf(
                'Successfully index %d events.',
                count($events)
            ));
            Log::notice('(CLI) Successfully index %d events.', [
                'numOfEvents' => count($events)
            ]);
            return 0;
        } catch (\Exception $e) {
            $this->error('Unable to index events. Reason: ' . $e->getMessage());
            Log::error('(CLI) Unable to index events', $e->getTrace());
            return 1;
        }
    }
}
