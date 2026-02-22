<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use ZipArchive;
use Carbon\Carbon;

class BackupDatabaseAndFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--retention= : Number of backups to keep (overrides config)} {--only-files : Skip DB dump and only archive files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of MySQL database and configured project paths (zip).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Backup: starting');
        $timestamp = Carbon::now()->format('Ymd_His');
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $config = config('backups');
        $sqlFile = $backupDir . DIRECTORY_SEPARATOR . "db_{$timestamp}.sql";

        if ($this->option('only-files')) {
            $this->info('Skipping database dump (only-files).');
        } else {
            $this->info('Dumping database...');
            $mysqlBin = env('MYSQL_BIN_PATH');
            // If not configured, try common mysqldump locations for XAMPP / Windows / Linux
            if (empty($mysqlBin)) {
                $candidates = [
                    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                    '/usr/bin/mysqldump',
                    '/usr/local/mysql/bin/mysqldump',
                    'mysqldump',
                ];
                foreach ($candidates as $c) {
                    if (str_contains($c, 'mysqldump') && (file_exists($c) || $c === 'mysqldump')) {
                        $mysqlBin = $c;
                        break;
                    }
                }
            }
            if (empty($mysqlBin)) {
                $mysqlBin = 'mysqldump';
            }
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');


            /**
             * Build command helper
             */
            $buildCmd = function ($host) use ($mysqlBin, $dbHost, $dbPort, $dbUser, $dbPass, $dbName, $sqlFile) {
                $cmd = [
                    $mysqlBin,
                    '--protocol=TCP',
                    '--host=' . $dbHost,
                    '--port=' . $dbPort,
                    '--user=' . $dbUser,
                    '--result-file=' . $sqlFile,
                    '--default-character-set=utf8mb4',
                    '--single-transaction',
                    '--routines',
                    '--triggers',
                    $dbName,
                ];

                // Safer password handling (Windows-friendly)
                if ($dbPass !== null && $dbPass !== '') {
                    $cmd[] = '--password';
                    $cmd[] = $dbPass;
                }

                return $cmd;
            };

            /**
             * Try 1 — TCP first (your production style)
             */
            $cmd = $buildCmd('127.0.0.1');
            $this->info("Trying TCP dump to 127.0.0.1...");

            // $process = new Process($cmd, base_path());
            // $process->setTimeout(600);
            // $process->run();

            // $dumpOk = $process->isSuccessful();

            // if (!$dumpOk) {
            //     $this->warn("TCP dump failed. Retrying using localhost socket...". $process->getErrorOutput());

            //     // Clean any empty file
            //     if (file_exists($sqlFile)) {
            //         @unlink($sqlFile);
            //     }

            //     /**
            //      * Try 2 — Fallback to localhost (socket/pipe)
            //      */
            //     $cmd = $buildCmd('localhost');

            //     $process = new Process($cmd, base_path());
            //     $process->setTimeout(600);
            //     $process->run();

            //     $dumpOk = $process->isSuccessful();

            //     if (!$dumpOk) {
            //         $this->error("Database dump failed on BOTH TCP and localhost.");
            //         $this->error("TCP Error: " . $process->getErrorOutput());

            //         // IMPORTANT: do NOT stop backup — continue with files only
            //         $this->warn("Proceeding with FILES-ONLY backup instead.");
            //     }
            // }

            // if ($dumpOk) {
            //     $this->info("Database dump saved to: {$sqlFile}");
            // } else {
            //     // Mark so we know later the DB is missing
            //     $sqlFile = null;
            // }

           // Build command string safely with escapeshellarg
            $mysqlBinSafe = escapeshellarg($mysqlBin);
            $dbHostSafe   = escapeshellarg($dbHost);
            $dbPortSafe   = escapeshellarg($dbPort);
            $dbUserSafe   = escapeshellarg($dbUser);
            $dbNameSafe   = escapeshellarg($dbName);
            $sqlFileSafe  = escapeshellarg($sqlFile);

            $command = "{$mysqlBinSafe} --host={$dbHostSafe} --port={$dbPortSafe} --user={$dbUserSafe} " .
                    "--default-character-set=utf8mb4 --single-transaction --routines --triggers {$dbNameSafe} > {$sqlFileSafe}";

            // Add password if set (still inline, but escaped)
            if (!empty($dbPass)) {
                $dbPassSafe = escapeshellarg($dbPass);
                $command = "{$mysqlBinSafe} --host={$dbHostSafe} --port={$dbPortSafe} --user={$dbUserSafe} " .
                        "--password={$dbPassSafe} --default-character-set=utf8mb4 --single-transaction --routines --triggers {$dbNameSafe} > {$sqlFileSafe}";
            }

            $this->info("Running mysqldump via exec...");

            // Redirect stderr to stdout so we capture all output
            exec($command . " 2>&1", $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error("Database dump failed: " . implode("\n", $output));
                // Clean up empty or failed dump file
                if (file_exists($sqlFile) && filesize($sqlFile) === 0) {
                    @unlink($sqlFile);
                }
                $sqlFile = null; // mark DB dump as failed
            } else {
                $this->info("Database dump saved to: {$sqlFile}");
            }


        }

        $zipFile = $backupDir . DIRECTORY_SEPARATOR . "backup_{$timestamp}.zip";
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            $this->error('Failed to create zip file: ' . $zipFile);
            return 1;
        }

        if (file_exists($sqlFile)) {
            $zip->addFile($sqlFile, basename($sqlFile));
        }

        foreach ($config['paths'] as $path) {
            $full = base_path($path);
            if (!file_exists($full)) {
                $this->warn('Skipping path (not found): ' . $full);
                continue;
            }

            if (is_dir($full)) {
                $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($full));
                foreach ($iterator as $file) {
                    if (!$file->isDir()) {
                        $localName = ltrim(str_replace(base_path(), '', $file->getPathname()), DIRECTORY_SEPARATOR);
                        $zip->addFile($file->getPathname(), $localName);
                    }
                }
            } else {
                $localName = ltrim(str_replace(base_path(), '', $full), DIRECTORY_SEPARATOR);
                $zip->addFile($full, $localName);
            }
        }

        $zip->close();

        // Remove temporary SQL file if exists
        if (file_exists($sqlFile)) {
            @unlink($sqlFile);
        }

        $this->info('Backup archive created: ' . 'storage/app/backups/' . basename($zipFile));

        // Retention
        $retention = $this->option('retention') ? (int)$this->option('retention') : (int)$config['retention'];
        $files = glob($backupDir . DIRECTORY_SEPARATOR . 'backup_*.zip');
        usort($files, function ($a, $b) { return filemtime($a) <=> filemtime($b); });
        if (count($files) > $retention) {
            $toDelete = array_slice($files, 0, count($files) - $retention);
            foreach ($toDelete as $f) {
                @unlink($f);
                $this->info('Removed old backup: ' . basename($f));
            }
        }

        $this->info('Backup: finished');
        return 0;
    }
}
