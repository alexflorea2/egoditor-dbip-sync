<?php

namespace App\Console;

use App\Console\Commands\Download;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('dbip:update --stage=fetch --auto=true')
            ->dailyAt('05:00')
            ->sendOutputTo(storage_path('import_log.txt'));

        $schedule->command('dbip:update --stage=unzip --auto=true')
            ->dailyAt('06:00')
            ->sendOutputTo(storage_path('import_log.txt'));

        $schedule->command('dbip:update --stage=insert --auto=true')
            ->dailyAt('07:00')
            ->sendOutputTo(storage_path('import_log.txt'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
