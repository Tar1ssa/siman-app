<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-logs:cleanup {--days=1825 : Number of days to keep logs (default: 1825 days = 5 years)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete activity logs older than specified days (default: 5 years)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Deleting activity logs older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')})...");

        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Successfully deleted {$deletedCount} old activity log records.");

        return Command::SUCCESS;
    }
}
