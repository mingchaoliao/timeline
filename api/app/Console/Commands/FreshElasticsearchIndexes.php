<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Repositories\SearchEventRepository;
use Illuminate\Support\Facades\Log;

class FreshElasticsearchIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recreate Elasticsearch index';

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
     * @param SearchEventRepository $eventRepository
     * @return mixed
     */
    public function handle(SearchEventRepository $eventRepository)
    {
        try {
            if ($eventRepository->hasEventIndex()) {
                $eventRepository->deleteEventIndex();

                $this->info('Index dropped: event');
                Log::notice('Index "event" dropped (CLI)');
            }

            $eventRepository->createEventIndex();

            $this->info(sprintf('Index created: event'));
            Log::notice('Index "event" created (CLI)');
        } catch (\Exception $e) {
            $message = sprintf(
                'failed to create index "event". Reason: %s',
                $e->getMessage()
            );
            $this->error($message);
            Log::error($message .' (CLI)');
            return 1;
        }

        return 0;
    }
}
