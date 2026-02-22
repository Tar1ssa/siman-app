<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check backup prerequisites and scheduler status.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Backup System Diagnostic ===');
        $this->newLine();

        // 1. Check mysqldump
        $this->info('1. Checking mysqldump...');
        $mysqlBin = env('MYSQL_BIN_PATH');
        if (empty($mysqlBin)) {
            $candidates = [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                '/usr/bin/mysqldump',
                '/usr/local/mysql/bin/mysqldump',
            ];
            foreach ($candidates as $c) {
                if (file_exists($c)) {
                    $mysqlBin = $c;
                    break;
                }
            }
        }

        if (empty($mysqlBin)) {
            $this->warn('   ⚠ mysqldump not found on system PATH.');
            $this->line('   Set MYSQL_BIN_PATH in .env, e.g.:');
            $this->line('   MYSQL_BIN_PATH="C:\\\\xampp\\\\mysql\\\\bin\\\\mysqldump.exe"');
            $mysqlStatus = false;
        } else {
            $this->info("   ✓ mysqldump found: {$mysqlBin}");
            $mysqlStatus = true;
        }
        $this->newLine();

        // 2. Check backup directory
        $this->info('2. Checking backup directory...');
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }
        if (is_dir($backupDir) && is_writable($backupDir)) {
            $this->info("   ✓ Backup directory writable: {$backupDir}");
            $dirStatus = true;
        } else {
            $this->error("   ✗ Backup directory not writable: {$backupDir}");
            $dirStatus = false;
        }
        $this->newLine();

        // 3. Check PHP extensions
        $this->info('3. Checking PHP extensions...');
        $required = ['zip', 'pdo', 'pdo_mysql'];
        $allLoaded = true;
        foreach ($required as $ext) {
            if (extension_loaded($ext)) {
                $this->info("   ✓ {$ext}");
            } else {
                $this->error("   ✗ {$ext} not loaded");
                $allLoaded = false;
            }
        }
        $this->newLine();

        // 4. List existing backups
        $this->info('4. Existing backups:');
        $files = glob($backupDir . DIRECTORY_SEPARATOR . 'backup_*.zip');
        if (empty($files)) {
            $this->line('   None yet.');
        } else {
            usort($files, function ($a, $b) { return filemtime($b) <=> filemtime($a); });
            foreach ($files as $f) {
                $size = round(filesize($f) / (1024 * 1024), 2) . ' MB';
                $time = date('Y-m-d H:i:s', filemtime($f));
                $this->line("   • " . basename($f) . " ({$size}) — {$time}");
            }
        }
        $this->newLine();

        // 5. Scheduler status & next run
        $this->info('5. Scheduler status:');
        $config = config('backups');
        $retention = $config['retention'];
        $this->line("   Cron Expression: 0 0 1 */3 * (1st of every 3rd month at 00:00)");
        $this->line("   Retention (keep): {$retention} backups");

        $next = $this->nextScheduledRun();
        $this->line("   Next scheduled run: {$next}");

        // Check if scheduler is likely active
        $isScheduled = $this->isSchedulerActive();
        if ($isScheduled) {
            $this->info("   ✓ Scheduler appears to be running.");
        } else {
            $this->warn("   ⚠ Scheduler may not be active. Set up Windows Task Scheduler or cron to run:");
            $this->line("      php artisan schedule:run");
        }
        $this->newLine();

        // 6. Summary
        $this->info('=== Summary ===');
        $allGood = $mysqlStatus && $dirStatus && $allLoaded;
        if ($allGood) {
            $this->info('✓ All prerequisites met. Backup system ready.');
            if (!$isScheduled) {
                $this->warn('⚠ Activate the scheduler to enable automatic 3-month backups.');
            }
            return 0;
        } else {
            $this->error('✗ Fix the issues above before running backup:run.');
            return 1;
        }
    }

    /**
     * Calculate the next scheduled run based on cron expression: 0 0 1 (every 3rd month) *
     */
    private function nextScheduledRun(): string
    {
        $now = Carbon::now();
        $next = $now->copy();

        // Find the next 1st day of every 3rd month
        // Months: Jan(1), Apr(4), Jul(7), Oct(10)
        $targetMonths = [1, 4, 7, 10];
        $currentMonth = $next->month;
        $currentYear = $next->year;

        // Find the next target month
        $found = false;
        foreach ($targetMonths as $m) {
            if ($m > $currentMonth) {
                $next = $next->setMonth($m)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);
                $found = true;
                break;
            }
        }

        if (!$found) {
            // Next target is in January of next year
            $next = $next->addYear()->setMonth(1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);
        }

        return $next->format('Y-m-d H:i:s');
    }

    /**
     * Heuristic check if scheduler is likely running (checks if last backup is recent).
     */
    private function isSchedulerActive(): bool
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . DIRECTORY_SEPARATOR . 'backup_*.zip');
        if (empty($files)) {
            return false;
        }

        usort($files, function ($a, $b) { return filemtime($b) <=> filemtime($a); });
        $lastBackupTime = filemtime($files[0]);
        $hoursSinceBackup = (time() - $lastBackupTime) / 3600;

        // If last backup was within 72 hours, assume scheduler is active
        return $hoursSinceBackup < 72;
    }
}
