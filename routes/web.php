<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bmnController;
use App\Http\Controllers\simanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\satkerController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\invalidController;
use App\Http\Controllers\InternalController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('siman', simanController::class);
Route::get('/siman-data/datatable', [simanController::class, 'datatable'])
    ->name('siman.datatable');
Route::delete('/siman', [SimanController::class, 'destroyBatch'])
    ->name('siman.destroyBatch');

Route::resource('internal', InternalController::class);
Route::get('/internal-data/datatable', [InternalController::class, 'datatable'])
    ->name('internal.datatable');
Route::get('/internal-data/make', [InternalController::class, 'make'])
    ->name('internal.make');
Route::delete('/internal', [InternalController::class, 'destroyBatch'])
    ->name('internal.destroyBatch');

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

Route::resource('dashboard', DashboardController::class);


Route::resource('bmn', bmnController::class);
Route::resource('satker', satkerController::class);
Route::resource('barang', BarangController::class);
