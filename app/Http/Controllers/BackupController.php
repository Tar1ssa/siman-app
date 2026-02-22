<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $title = 'Backups Dashboard';
        $backupDir = storage_path('app/backups');

        $files = glob($backupDir . DIRECTORY_SEPARATOR . 'backup_*.zip');

        usort($files, function ($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });

        $backups = collect($files)->map(function ($file) {
            return [
                'name' => basename($file),
                'size' => round(filesize($file) / (1024 * 1024), 2) . ' MB',
                'created_at' => Carbon::createFromTimestamp(filemtime($file))
                    ->format('Y-m-d H:i:s'),
            ];
        });

        $lastBackup = $backups->first()['created_at'] ?? 'Never';

        return view('backups.index', compact('backups', 'lastBackup','title'));
    }

    public function runFullBackup()
    {
        Artisan::call('backup:run');
        // dd(Artisan::output());
        return redirect()->back()
            ->with('success', 'Full backup started in background.');
    }

    public function runFilesOnly()
    {
        Artisan::call('backup:run --only-files');
        // dd(Artisan::output());

        return redirect()->back()
            ->with('success', 'Files-only backup started.');
    }

    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($path);
    }

    public function destroy($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        unlink($path);

        return redirect()->back()->with('success', 'Backup deleted successfully.');
    }
}
