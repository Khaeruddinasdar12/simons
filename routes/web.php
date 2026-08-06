<?php

use App\Http\Controllers\PermohonanPembimbingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PermohonanPembimbingController::class, 'create'])->name('home');
Route::post('/permohonan-pembimbing', [PermohonanPembimbingController::class, 'store'])
    ->name('permohonan.store');

Route::get('/tracking', [PermohonanPembimbingController::class, 'tracking'])
    ->name('permohonan.tracking');

Route::get('/sk/verifikasi/{token}', [PermohonanPembimbingController::class, 'verifySk'])
    ->name('sk.verify');

Route::get('/sk/unduh/{permohonan}', [PermohonanPembimbingController::class, 'downloadSk'])
    ->name('sk.download');

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
});
