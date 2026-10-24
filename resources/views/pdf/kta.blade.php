<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KTA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 90mm;
            height: 55mm;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        .kta {
            width: 90mm;
            height: 55mm;
            position: relative;
            overflow: hidden;
        }

        /* Background */
        .kta-bg {
            position: absolute;
            top: 0; left: 0;
            width: 90mm; height: 55mm;
            z-index: 0;
        }
        .kta-bg img {
            width: 90mm; height: 55mm;
            display: block;
        }

        /* Overlay konten */
        .kta-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 90mm; height: 55mm;
            z-index: 10;
        }

        /* Area konten mulai dari bawah header background (~10mm) */
        .content-body {
            position: absolute;
            top: 10mm; left: 0; right: 0; bottom: 0;
        }

        .content-body > table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Kolom foto */
        .td-foto {
            width: 25mm;
            vertical-align: top;
            padding: 2mm 1mm 2mm 6mm;
        }

        .foto-frame {
            width: 19mm;
            height: 25mm;
            border: 0.4mm solid #1b5e20;
            overflow: hidden;
            background: rgba(255,255,255,0.5);
        }
        .foto-frame img {
            width: 100%; height: 100%;
            display: block; object-fit: cover;
        }

        /* Kolom info */
        .td-info {
            vertical-align: top;
            padding: 2mm 2mm 1mm 0;
        }

        .judul {
            font-size: 8pt;
            font-weight: 900;
            color: #111;
            margin-bottom: 1.5mm;
            line-height: 1;
        }

        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td {
            font-size: 5pt;
            color: #111;
            padding: 0.4mm 0;
            vertical-align: top;
            line-height: 1.2;
        }
        .lbl { font-weight: bold; width: 16mm; white-space: nowrap; }
        .sep { width: 2mm; }

        /* TTD */
        .ttd-wrap { margin-top: 1.5mm; text-align: center; }
        .ttd-org {
            font-size: 4.5pt;
            font-weight: bold;
            color: #1b5e20;
            line-height: 1.3;
        }
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 1mm; }
        .ttd-table td {
            text-align: center;
            font-size: 4.5pt;
            padding: 0 1mm;
            vertical-align: bottom;
            width: 50%;
        }
        .ttd-jabatan { font-weight: bold; color: #111; display: block; }
        .ttd-img-wrap { height: 6mm; display: block; text-align: center; }
        .ttd-img-wrap img {
            max-height: 6mm; max-width: 90%;
            object-fit: contain; display: inline-block;
        }
        .ttd-nama {
            display: block;
            font-size: 4.5pt;
            font-weight: bold;
            color: #111;
            /* border-top: 0.3mm solid #333; */
            /* padding-top: 0.3mm;
            margin-top: 0.3mm; */
        }
    </style>
</head>
<body>
<div class="kta">

    @if(!empty($bgBase64))
        <div class="kta-bg">
            <img src="{{ $bgBase64 }}" alt="">
        </div>
    @endif

    <div class="kta-overlay">
        <div class="content-body">
            <table>
                <tr>
                    <td class="td-foto">
                        <div class="foto-frame">
                            @if(!empty($fotoAnggota))
                                <img src="{{ $fotoAnggota }}" alt="Foto">
                            @endif
                        </div>
                    </td>

                    <td class="td-info">
                        <div class="judul">KARTU REGISTRASI</div>

                        <table class="info-table">
                            <tr>
                                <td class="lbl">Nama</td>
                                <td class="sep">:</td>
                                <td>{{ $anggota->nama_lengkap ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">No. Registrasi</td>
                                <td class="sep">:</td>
                                <td>{{ $anggota->nia ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Kecamatan</td>
                                <td class="sep">:</td>
                                <td>{{ $anggota->kecamatan->nama_kecamatan ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Desa/Kel.</td>
                                <td class="sep">:</td>
                                <td>{{ $anggota->desa->nama_desa ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Keanggotaan</td>
                                <td class="sep">:</td>
                                <td>Kader</td>
                            </tr>
                           @if($kta->tanggal_berlaku_sampai)
<tr>
    <td class="lbl">Masa Berlaku</td>
    <td class="sep">:</td>
    <td>Sampai {{ $kta->tanggal_berlaku_sampai->format('d/m/Y') }}</td>
</tr>
@endif
                        </table>

                        <div class="ttd-wrap">
                            <div class="ttd-org">
                                PIMPINAN CABANG GP. ANSOR KAB. KUDUS
                            </div>
                            <table class="ttd-table">
                                <tr>
                                    <td>
                                        <span class="ttd-jabatan">KETUA</span>
                                        <span class="ttd-img-wrap">
                                            @if(!empty($ttdKetua))
                                                <img src="{{ $ttdKetua }}" alt="TTD Ketua">
                                            @endif
                                        </span>
                                        <span class="ttd-nama">{{ $kta->nama_ketua ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span class="ttd-jabatan">SEKRETARIS</span>
                                        <span class="ttd-img-wrap">
                                            @if(!empty($ttdSekretaris))
                                                <img src="{{ $ttdSekretaris }}" alt="TTD Sekretaris">
                                            @endif
                                        </span>
                                        <span class="ttd-nama">{{ $kta->nama_sekretaris ?? '' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>
</body>
</html>