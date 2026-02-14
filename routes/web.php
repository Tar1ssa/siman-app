<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\bmnController;
use App\Http\Controllers\simanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\satkerController;
use App\Http\Controllers\AtributController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\invalidController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdentitasController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('siman', simanController::class);
Route::get('/siman-data/datatable', [simanController::class, 'datatable'])
    ->name('siman.datatable');
Route::delete('/siman', [SimanController::class, 'destroyBatch'])
    ->name('siman.destroyBatch');

Route::resource('identitas', IdentitasController::class);
Route::resource('atribut', AtributController::class);

Route::resource('internal', InternalController::class);
Route::get('identitas/{identitas}/atribut', [IdentitasController::class, 'atributByIdentitas'])
    ->name('identitas.atribut');

Route::get('/internal-data/datatable', [InternalController::class, 'datatable'])
    ->name('internal.datatable');
Route::get('/internal-data/make', [InternalController::class, 'make'])
    ->name('internal.make');
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
Route::resource('lokasi', LokasiController::class);


// daeng babi
