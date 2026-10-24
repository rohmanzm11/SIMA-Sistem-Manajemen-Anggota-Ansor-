<?php

namespace App\Http\Controllers;

use App\Models\PrintLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use ZipArchive;

class ExportDownloadController extends Controller
{
    public function __invoke(string $token)
    {
        $payload = Cache::pull("export:{$token}");
        abort_if(! $payload, 404, 'Link export sudah kedaluwarsa.');

        $logs = PrintLog::with(['anggota', 'pelatihanDetail.pelatihan', 'templateSertifikat', 'kta'])
            ->whereIn('id', $payload['ids'])
            ->get();

        $zipPath = storage_path("app/export-{$payload['jenis']}-" . now()->format('Ymd-His') . '.zip');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($logs as $log) {
            $nama = str($log->anggota?->nama_lengkap ?? 'anggota')->slug();
            $zip->addFromString("{$payload['jenis']}-{$nama}-{$log->id}.pdf", $this->renderPdf($log)->output());
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    protected function renderPdf(PrintLog $log)
    {
        // TODO: pakai view & ukuran kertas yang sama dengan route
        // /kta/pdf/{id} dan print-logs.pdf milik Anda
        $view = $log->jenis_cetakan === 'KTA' ? 'pdf.kta' : 'pdf.sertifikat';

        return Pdf::loadView($view, ['log' => $log])->setPaper('a4', 'landscape');
    }
}