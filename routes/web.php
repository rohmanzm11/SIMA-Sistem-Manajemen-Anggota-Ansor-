<?php

use App\Http\Controllers\ExportDownloadController;
use App\Http\Controllers\KtaPdfController;
use App\Http\Controllers\PrintLogController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::middleware('auth')->group(function () {
    Route::get('/kta/pdf/{id}', [KtaPdfController::class, 'generate'])
        ->name('kta.pdf');

    Route::get('/sertifikat/pdf/{id}', [PrintLogController::class, 'generatePdf'])
        ->name('print-logs.pdf');

    Route::get('/export/download/{token}', ExportDownloadController::class)
        ->name('export.download');
});