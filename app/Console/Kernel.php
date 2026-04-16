<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;

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
        \App\Console\Commands\BackupDatabaseAndFiles::class,
        \App\Console\Commands\BackupCheck::class,
        \App\Console\Commands\DeleteOldActivityLogs::class,
        \App\Console\Commands\RunTestsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run backup every 3 months: Jan 1, Apr 1, Jul 1, Oct 1 at 00:00
        $schedule->command('backup:run')
            ->cron('0 0 1 */3 *')
            ->withoutOverlapping()
            ->onFailure(function () {
                Log::error(' Automated 3-month backup failed.');
            })
            ->onSuccess(function () {
                Log::info(' Automated 3-month backup completed.');
            });

        // Clean up old activity logs daily at 01:00
        $schedule->command('activity-logs:cleanup')
            ->dailyAt('01:00')
            ->withoutOverlapping()
            ->onFailure(function () {
                Log::error('Activity logs cleanup failed.');
            })
            ->onSuccess(function () {
                Log::info('Activity logs cleanup completed.');
            });
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
