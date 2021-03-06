<?php

namespace App\Console;

use App\Jobs\CleanUnlinkedImages;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('horizon:snapshot')->everyMinute();
        $schedule->job(CleanUnlinkedImages::class)->daily();
        if(env('APP_BACKUP', false) === true) {
            $schedule->command('backup:clean')->daily()->at('01:00');
            $schedule->command('backup:run')
                ->daily()
                ->at('02:00')
                ->after(function () {
                    Log::info('DB/storage backup complete');
                });
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
