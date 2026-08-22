<?php

use App\Http\Controllers\PermohonanPembimbingController;
use App\Http\Controllers\PermohonanPengujiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'permohonan.alur')->name('home');

Route::get('/permohonan-pembimbing', [PermohonanPembimbingController::class, 'create'])
    ->name('pembimbing.create');
Route::post('/permohonan-pembimbing', [PermohonanPembimbingController::class, 'store'])
    ->name('permohonan.store');

Route::get('/permohonan-penguji', [PermohonanPengujiController::class, 'create'])
    ->name('penguji.create');
Route::get('/permohonan-penguji/lookup', [PermohonanPengujiController::class, 'lookup'])
    ->name('penguji.lookup');
Route::post('/permohonan-penguji', [PermohonanPengujiController::class, 'store'])
    ->name('penguji.store');

Route::get('/tracking', [PermohonanPembimbingController::class, 'tracking'])
    ->name('permohonan.tracking');

Route::get('/sk/verifikasi/{token}', [PermohonanPembimbingController::class, 'verifySk'])
    ->name('sk.verify');
Route::get('/sk/unduh/{permohonan}', [PermohonanPembimbingController::class, 'downloadSk'])
    ->name('sk.download');

Route::get('/sk-penguji/verifikasi/{token}', [PermohonanPengujiController::class, 'verifySk'])
    ->name('sk.penguji.verify');
Route::get('/sk-penguji/unduh/{permohonanPenguji}', [PermohonanPengujiController::class, 'downloadSk'])
    ->name('sk.penguji.download');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/sk/preview/{permohonan}', [PermohonanPembimbingController::class, 'previewSk'])
        ->name('sk.preview');
    Route::get('/sk/lihat/{permohonan}', [PermohonanPembimbingController::class, 'lihatSk'])
        ->name('sk.lihat');

    Route::get('/sk-penguji/preview/{permohonanPenguji}', [PermohonanPengujiController::class, 'previewSk'])
        ->name('sk.penguji.preview');
    Route::get('/sk-penguji/lihat/{permohonanPenguji}', [PermohonanPengujiController::class, 'lihatSk'])
        ->name('sk.penguji.lihat');
});
