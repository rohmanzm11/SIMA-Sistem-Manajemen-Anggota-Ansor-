<?php

use App\Filament\Pages\PesertaEdit;
use App\Http\Controllers\KtaPdfController;
use App\Http\Controllers\PrintLogController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect('/admin/login');
});


// Route::get('/print-logs/{id}/pdf', [PrintLogController::class, 'generatePdf'])
//     ->name('print-logs.pdf')
//     ->middleware(['auth']);
Route::middleware(['auth'])->group(function () {

    Route::get('/kta/pdf/{id}', [KtaPdfController::class, 'generate'])->middleware('auth');
    Route::get('/sertifikat/pdf/{id}', [PrintLogController::class, 'generatePdf'])
        ->name('print-logs.pdf');
    // Route::get('/admin/peserta/edit', PesertaEdit::class)
    //     ->name('filament.admin.pages.peserta-edit')
    //     ->middleware('auth');
});
