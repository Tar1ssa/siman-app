<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bmnController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\simanController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\satkerController;
use App\Http\Controllers\AtributController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\invalidController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdentitasController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\LockedDataController;
use App\Http\Controllers\UnitTeknisController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\IdentitasKategoriController;

Route::get('/', function () {
    return view('login');
});

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('actionLogin', [LoginController::class, 'actionLogin'])->middleware('throttle:5,1')->name('actionLogin');

Route::middleware('auth')->group(function () {


    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard/kondisi-barang-status', [DashboardController::class, 'kondisiBarangStatus'])->name('dashboard.kondisi-barang-status');
    Route::resource('dashboard', DashboardController::class);

    // Admin Routes
    Route::middleware('role:administrator')->group(function () {
        Route::get('/admin/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/admin/backups/full', [BackupController::class, 'runFullBackup']);
        Route::post('/admin/backups/files-only', [BackupController::class, 'runFilesOnly']);
        Route::get('/admin/backups/download/{filename}',
            [BackupController::class, 'download'])
            ->name('backups.download');
        Route::delete('/admin/backups/delete/{filename}',
            [BackupController::class, 'destroy'])
            ->name('backups.delete');

        Route::resource('bmn', bmnController::class);
        Route::resource('satker', satkerController::class);
        Route::resource('barang', BarangController::class);
        Route::resource('lokasi', LokasiController::class);
        Route::resource('identitas-kategori', IdentitasKategoriController::class);
        Route::resource('identitas', IdentitasController::class);
        Route::resource('atribut', AtributController::class);
        Route::resource('unitkerja', UnitKerjaController::class);
        Route::resource('unitteknis', UnitTeknisController::class);
        Route::resource('user', UserController::class);

        Route::get('/internal/locked', [LockedDataController::class, 'index'])
            ->name('internal.locked');
        Route::get('/internal/locked/datatable', [LockedDataController::class, 'datatable'])
            ->name('internal.locked.datatable');
        Route::put('/internal/{id}/lock', [LockedDataController::class, 'lock'])
            ->name('internal.lock');
        Route::put('/internal/{id}/unlock', [LockedDataController::class, 'unlock'])
            ->name('internal.unlock');
        Route::put('/internal/{id}/reject-request', [LockedDataController::class, 'rejectRequest'])
            ->name('internal.reject-request');


        Route::get('/activity-logs/datatable', [ActivityLogController::class, 'datatable'])
            ->name('activity-logs.datatable');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])
            ->name('activity-logs.export');
        Route::post('/activity-logs/cleanup', [ActivityLogController::class, 'cleanup'])
            ->name('activity-logs.cleanup');
        Route::resource('activity-logs', ActivityLogController::class);

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::put('/internal/{id}/requestUnlock', [LockedDataController::class, 'requestUnlock'])
        ->name('internal.requestUnlock');

    Route::resource('siman', simanController::class);
    Route::get('/siman-data/datatable', [simanController::class, 'datatable'])
        ->name('siman.datatable');
    Route::delete('/siman/batch/delete', [SimanController::class, 'destroyBatch'])
        ->name('siman.destroyBatch');

    Route::resource('internal', InternalController::class);
    Route::get('/identitas/bykategori/{id}', [IdentitasController::class, 'byKategori']);
    Route::get('identitas/{identitas}/atribut', [IdentitasController::class, 'atributByIdentitas'])
        ->name('identitas.atribut');

    Route::get('/internal-data/datatable', [InternalController::class, 'datatable'])
        ->name('internal.datatable');
    Route::get('/internal-data/make', [InternalController::class, 'make'])
        ->name('internal.make');
    Route::get('/identitas/kategori/{id}', [InternalController::class, 'kategoriIdentitas'])
        ->name('internal.kategoriIdentitas');
    Route::post('/internal/insert', [InternalController::class, 'insert'])
        ->name('internal.insert');
    Route::delete('/internal/batch/delete', [InternalController::class, 'destroyBatch'])
        ->name('internal.destroyBatch');

    Route::post('/internal/images/store', [InternalController::class, 'addImage'])
        ->name('internal.addImage');
    Route::put('/internal/images/{id}/update', [InternalController::class, 'updateImage'])
        ->name('internal.updateImage');
    Route::delete('/internal/images/{id}/delete', [InternalController::class, 'imageDestroy'])
        ->name('internal.imageDestroy');

    Route::post('/internal/documents/store', [InternalController::class, 'addDocument'])
        ->name('internal.addDocument');
    Route::put('/internal/documents/{id}/update', [InternalController::class, 'updateDocument'])
        ->name('internal.updateDocument');
    Route::delete('/internal/documents/{id}/delete', [InternalController::class, 'documentDestroy'])
        ->name('internal.documentDestroy');

    Route::get('/internal/bast/{id}', [InternalController::class, 'downloadBast'])
        ->name('internal.bast');

    Route::get('/export-data/internal-all', [InternalController::class, 'exportAll'])
        ->name('export.internal-all');




    Route::resource('compare', CompareController::class);
    Route::get('/compare-data/datatable', [CompareController::class, 'datatable'])
        ->name('compare.datatable');
    Route::get('/export-data/internal', [CompareController::class, 'exportInternalOnly'])
        ->name('export.internal');
    Route::get('/export-data/siman', [CompareController::class, 'exportSimanOnly'])
        ->name('export.siman');
    Route::get('/export-data/match-tgl-misnilai', [CompareController::class, 'exportMatchTgl'])
        ->name('export.matchtgl');
    Route::get('/export-data/match-nilai-mistgl', [CompareController::class, 'exportMatchNilai'])
        ->name('export.matchnilai');
    Route::get('/export-data/match', [CompareController::class, 'exportMatch'])
        ->name('export.match');
    Route::get('/export-data/match-nup-mistgl-misnilai', [CompareController::class, 'exportNupMatch'])
        ->name('export.matchnup');



    Route::resource('invalid', invalidController::class);
    Route::get('/invalid-data/datatable', [invalidController::class, 'datatable'])
        ->name('invalid.datatable');
    Route::delete('/invalid', [invalidController::class, 'destroyBatch'])
        ->name('invalid.destroyBatch');
    Route::get('/export-data/invalid', [invalidController::class, 'exportInvalid'])
        ->name('export.invalid');


});





