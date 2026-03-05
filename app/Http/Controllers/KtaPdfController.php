<?php

namespace App\Http\Controllers;

use App\Models\Kta;
use App\Models\PrintLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class KtaPdfController extends Controller
{
    public function generate(int $id)
    {
        $printLog = PrintLog::with([
            'anggota.kecamatan',
            'anggota.desa',
            'kta',
        ])->findOrFail($id);

        $anggota = $printLog->anggota;

        // Ambil KTA dari relasi printLog
        // Jika kta_id null ATAU image kosong, fallback ke KTA aktif terbaru
        $kta = $printLog->kta;

        if (!$kta || empty($kta->image)) {
            $ktaFallback = Kta::where('is_active', true)->latest()->first()
                ?? Kta::latest()->first();

            if ($ktaFallback) {
                if (!$kta) {
                    $kta = $ktaFallback;
                } else {
                    $kta->image = $ktaFallback->image;
                }
            }
        }

        $bgBase64      = $this->toBase64($kta?->image);
        $fotoAnggota   = $this->toBase64($anggota?->foto);
        $ttdKetua      = $this->toBase64($kta?->ttd_ketua);
        $ttdSekretaris = $this->toBase64($kta?->ttd_sekretaris);

        $pdf = Pdf::loadView('pdf.kta', [
            'anggota'       => $anggota,
            'kta'           => $kta,
            'bgBase64'      => $bgBase64,
            'fotoAnggota'   => $fotoAnggota,
            'ttdKetua'      => $ttdKetua,
            'ttdSekretaris' => $ttdSekretaris,
        ])
            ->setPaper([0, 0, 255.12, 155.91])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 150,
            ]);

        $filename = 'KTA_' . str_replace(' ', '_', $anggota->nama_lengkap ?? 'anggota') . '.pdf';

        return $pdf->download($filename);
    }

    private function toBase64(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $content = null;
        $ext     = strtolower(pathinfo($value, PATHINFO_EXTENSION));

        // 1. Via Storage::disk('public')
        try {
            if (Storage::disk('public')->exists($value)) {
                $content = Storage::disk('public')->get($value);
            }
        } catch (\Exception) {
        }

        // 2. Fallback ke berbagai path absolut
        if ($content === null) {
            $candidates = [
                storage_path('app/public/' . $value),
                public_path('storage/' . $value),
                public_path($value),
                storage_path('app/' . $value),
                public_path('storage/kta-images/' . basename($value)),
                storage_path('app/public/kta-images/' . basename($value)),
                public_path('storage/ttd-kta/' . basename($value)),
                storage_path('app/public/ttd-kta/' . basename($value)),
            ];

            foreach ($candidates as $path) {
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    break;
                }
            }
        }

        if ($content === null) {
            return null;
        }

        $mime = match ($ext) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
            default => 'image/jpeg',
        };

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }
}
