<?php

namespace App\Http\Controllers;

use App\Models\PrintLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PrintLogController extends Controller
{
    public function generatePdf(int $id)
    {
        $printLog = PrintLog::with([
            'anggota.kecamatan',
            'anggota.desa',
            'pelatihanDetail.pelatihan.pelatihanDetails.materi',
            'templateSertifikat',
            'pencetak',
            'kta',
        ])->findOrFail($id);

        if ($printLog->jenis_cetakan === 'Sertifikat') {
            return $this->generateSertifikat($printLog);
        }

        return $this->generateKta($printLog);
    }

    private function generateSertifikat(PrintLog $printLog)
    {
        $materiList = collect();
        if ($printLog->pelatihanDetail && $printLog->pelatihanDetail->pelatihan) {
            $materiList = $printLog->pelatihanDetail->pelatihan
                ->pelatihanDetails()
                ->with('materi')
                ->get();
        }

        $bgBase64 = null;
        if ($printLog->templateSertifikat?->image) {
            $bgBase64 = $this->convertImageToBase64($printLog->templateSertifikat->image);
        }

        // Generate QR Code base64 dari NIA anggota
        $qrBase64 = null;
        $nia = $printLog->anggota->nia ?? $printLog->anggota->nomor_registrasi ?? null;
        if ($nia) {
            $qrBase64 = $this->generateQrCode($nia);
        }

        $pdf = Pdf::loadView('pdf.sertifikat', [
            'printLog'   => $printLog,
            'anggota'    => $printLog->anggota,
            'pelatihan'  => $printLog->pelatihanDetail?->pelatihan,
            'detail'     => $printLog->pelatihanDetail,
            'template'   => $printLog->templateSertifikat,
            'materiList' => $materiList,
            'bgBase64'   => $bgBase64,
            'qrBase64'   => $qrBase64,
        ])
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        $filename = 'Sertifikat_' . str_replace(' ', '_', $printLog->anggota->nama_lengkap ?? 'anggota') . '.pdf';

        return $pdf->download($filename);
    }

    private function generateKta(PrintLog $printLog)
    {
        $bgBase64    = null;
        $fotoAnggota = null;

        if ($printLog->kta?->image) {
            $bgBase64 = $this->convertImageToBase64($printLog->kta->image);
        }

        if ($printLog->anggota?->foto) {
            $fotoAnggota = $this->convertImageToBase64($printLog->anggota->foto);
        }

        $pdf = Pdf::loadView('pdf.kta', [
            'printLog'    => $printLog,
            'anggota'     => $printLog->anggota,
            'kta'         => $printLog->kta,
            'nia'         => $printLog->anggota->nia ?? '—',
            'bgBase64'    => $bgBase64,
            'fotoAnggota' => $fotoAnggota,
        ])
            ->setPaper([0, 0, 255.15, 155.9])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        $filename = 'KTA_' . str_replace(' ', '_', $printLog->anggota->nama_lengkap ?? 'anggota') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate QR Code sebagai base64 dari NIA.
     *
     * Menggunakan library chillerlan/php-qrcode (direkomendasikan).
     * Install via: composer require chillerlan/php-qrcode
     *
     * Fallback: menggunakan Google Charts API (butuh isRemoteEnabled = true).
     */
    private function generateQrCode(string $nia): ?string
    {
        // URL yang akan di-encode ke QR (sesuaikan dengan URL verifikasi Anda)
        $verifikasiUrl = url('/verifikasi/sertifikat/' . urlencode($nia));

        // Opsi 1: Library lokal chillerlan/php-qrcode (lebih andal & offline)
        if (class_exists('\Chillerlan\QRCode\QRCode')) {
            try {
                $options = new \Chillerlan\QRCode\QROptions([
                    'outputType' => \Chillerlan\QRCode\Output\QROutputInterface::GDIMAGE_PNG,
                    'imageBase64' => true,
                    'scale' => 5,
                    'quietzoneSize' => 2,
                    'moduleValues' => [
                        // warna modul (hitam)
                        \Chillerlan\QRCode\Data\QRMatrix::M_DATA_DARK => [0, 0, 0],
                    ],
                ]);
                $qr = new \Chillerlan\QRCode\QRCode($options);
                return $qr->render($verifikasiUrl); // sudah dalam format data:image/png;base64,...
            } catch (\Throwable $e) {
                // fallback ke Google Charts
            }
        }

        // Opsi 2: Simpan QR dari Google Charts API ke local temp (butuh isRemoteEnabled)
        // Return null, dan di blade akan render via <img src="..."> langsung
        return null;
    }

    /**
     * Konversi image ke base64 — cek semua kemungkinan path
     */
    private function convertImageToBase64(?string $value): ?string
    {
        if (empty($value)) return null;

        // 1. Cek via Storage::disk('public')
        try {
            if (Storage::disk('public')->exists($value)) {
                $content = Storage::disk('public')->get($value);
                $ext     = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                $mime    = match ($ext) {
                    'png'  => 'image/png',
                    'webp' => 'image/webp',
                    'gif'  => 'image/gif',
                    default => 'image/jpeg',
                };
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        } catch (\Exception $e) {
            //
        }

        // 2. Cek path absolut
        $candidates = [
            public_path('storage/' . $value),
            public_path($value),
            storage_path('app/public/' . $value),
            storage_path('app/' . $value),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'png'  => 'image/png',
                    'webp' => 'image/webp',
                    'gif'  => 'image/gif',
                    default => 'image/jpeg',
                };
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        return null;
    }
}
