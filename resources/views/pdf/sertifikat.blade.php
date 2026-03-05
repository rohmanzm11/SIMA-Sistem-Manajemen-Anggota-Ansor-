<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cookie&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Felipa&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 0;
            size: 297mm 210mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

       /* =================== HALAMAN DEPAN =================== */
        .page-depan {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        .bg-image {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        /* ZONA ATAS: logo → penyelenggara */
        .zona-atas {
            position: absolute;
            top: 3mm;
            left: 30mm;
            right: 30mm;
            height: 148mm;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            text-align: center;
        }

        .logo-area {
            margin-top: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .logo-area img {
            width: 52px;
            height: auto;
        }
        .org-name {
            font-size: 11px;
            font-weight: bold;
            color: #2d6a2d;
            letter-spacing: 0.5px;
            line-height: 1.4;
            margin-top: 2px;
        }

        .title-sertifikat {
            font-size: 40px;
            font-weight: 900;
            color: #1a4a1a;
            letter-spacing: 10px;
            margin: 50mm 0 1mm 0;
            font-family: "Cookie", cursive;
        }

        .subtitle {
            font-size: 18px;
            color: #555;
            margin-bottom: 2mm;
            
        }

        .nama-anggota {
            font-size: 45px;
            color: #2d6a2d;
            font-style: italic;
            font-family: "Felipa", cursive;
            margin: 0 0 1mm 0;
            padding-bottom: 3px;
            border-bottom: 2px solid #2d6a2d;
            min-width: 200px;
            display: inline-block;
        }

        .info-anggota {
            font-size: 18px;
            color: #444;
            margin: 1.5mm 0;
            line-height: 1.5;
        }

        .keterangan {
            font-size: 20px;
            color: #222;
            margin: 1.5mm 0;
            line-height: 1.7;
        }
        .keterangan strong {
            color: #2d6a2d;
            font-size: 30px;
        }

        .penyelenggara {
            font-size: 20px;
            font-weight: bold;
            color: #2d6a2d;
            margin: 1.5mm 0 0 0;
            line-height: 1.5;
        }

        /* ZONA BAWAH: TTD */
        .zona-ttd {
            position: absolute;
            top: 155mm;
            left: 0;
            right: 0;
            height: 80mm;
            z-index: 10;
            display: table;
            table-layout: fixed;
            width: 100%;
            padding: 0 20mm;
        }

        .ttd-box {
            display: table-cell;
            text-align: center;
            vertical-align: top;
            width: 50%;
            padding: 0 10mm;
        }

        .ttd-img {
            height: 90px;
            width: auto;
            max-width: 100%;
            display: block;
            margin: 0 auto 3px auto;
        }

        .ttd-space-fallback { height: 45px; }

        .ttd-line {
            /* border-top: 1px solid #444;
            padding-top: 4px; */
        }

        .ttd-nama {
            font-size: 20px;
            font-weight: bold;
            color: #1a4a1a;
        }
        .ttd-jabatan {
            font-size: 25px;
            color: #555;
        }

        /* =================== HALAMAN BELAKANG =================== */

        /* ── Halaman container ── */
        .page-belakang {
            width: 100%;
            height: 210mm;
            background: #ffffff;
            box-sizing: border-box;
        }

        /*
         * LAYOUT UTAMA — 3 baris: header / body / footer
         * Direalisasikan sebagai outer-table dengan 3 <tr>
         *   row 1  → HEADER  : data peserta (lebar penuh)
         *   row 2  → BODY    : tabel materi (lebar penuh, mengisi sisa ruang)
         *   row 3  → FOOTER  : TTD instruktur (kiri) + QR (kanan)
         */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            padding: 7mm 12mm 6mm 12mm;
        }
        .layout-table > tbody > tr > td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        /* ── ROW 1: HEADER — data peserta ── */
        .row-header {
            padding-bottom: 5px;
        }

        /* Judul kegiatan */
        .belakang-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            color: #1a4a1a;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 4px 0 5px 0;
            margin-bottom: 7px;
            border-bottom: 2.5px solid #2d6a2d;
        }

        /* Tabel data peserta */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            color: #222;
        }
        .info-table td {
            padding: 2px 3px;
            vertical-align: top;
            line-height: 1.65;
        }
        .info-table .label-col {
            white-space: nowrap;
            font-weight: bold;
            color: #2d6a2d;
            width: 115px;
        }
        .info-table .colon-col {
            white-space: nowrap;
            width: 10px;
            padding: 2px 5px;
        }
        .info-table .value-col { color: #333; }

        /* Garis pemisah header → body */
        .header-divider {
            border: none;
            border-top: 1px solid #d5e8d5;
            margin: 6px 0 5px 0;
        }

        /* ── ROW 2: BODY — tabel materi ── */
        .row-body {
            padding-bottom: 6px;
        }

        .materi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            table-layout: fixed;
        }
        .materi-table thead tr {
            background-color: #1e5c1e;
        }
        .materi-table th {
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 10.5px;
            color: #ffffff;
            letter-spacing: 0.3px;
            border: 1px solid #1e5c1e;
        }
        .materi-table tbody td {
            border-bottom: 1px solid #e0e0e0;
            border-left:   1px solid #e8e8e8;
            border-right:  1px solid #e8e8e8;
            border-top: none;
            padding: 5px 8px;
            font-size: 10.5px;
            vertical-align: middle;
            color: #222;
        }
        .materi-table .col-no     { text-align: center; color: #555; }
        .materi-table .col-durasi { text-align: center; }
        .materi-table tbody tr.even { background-color: #f0f7f0; }
        .materi-table tfoot td {
            border: 1px solid #2d6a2d;
            background-color: #e0ede0;
            padding: 5px 8px;
            font-size: 10.5px;
            font-weight: bold;
            color: #1a4a1a;
        }

        /* ── ROW 3: FOOTER — TTD kiri + QR kanan ── */
        .row-footer { padding-top: 10px; }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .footer-table > tbody > tr > td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .footer-ttd { width: 58%; padding-right: 8px; }
        .footer-qr  { width: 42%; text-align: center; }

        /* TTD instruktur */
        .ttd-instruktur { font-size: 10px; color: #333; line-height: 1.75; }
        .ttd-instruktur .tim-title {
            font-weight: bold;
            font-size: 10px;
            color: #1a4a1a;
            line-height: 1.75;
        }
        .ttd-space   { height: 28px; display: block; }
        .nama-ttd {
            font-weight: bold;
            border-top: 1.5px solid #2d6a2d;
            padding-top: 3px;
            display: inline-block;
            min-width: 150px;
            font-size: 10px;
            color: #1a4a1a;
        }
        .jabatan-ttd {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* QR code */
        .qr-area    { text-align: center; }
        .qr-box {
            display: inline-block;
            border: 1.5px solid #2d6a2d;
            border-radius: 4px;
            padding: 4px;
            background: #fff;
        }
        .qr-area img {
            width: 68px;
            height: 68px;
            display: block;
            margin: 0 auto;
        }
        .qr-label {
            font-size: 8px;
            color: #555;
            line-height: 1.6;
            margin-top: 4px;
            text-align: center;
        }
        .qr-label strong { color: #2d6a2d; font-size: 8px; }

    </style>
</head>
<body>

{{-- =================== HALAMAN DEPAN =================== --}}
<div class="page-depan">

    @if(!empty($bgBase64))
        <img class="bg-image" src="{{ $bgBase64 }}" alt="background">
    @endif

    {{-- ZONA ATAS --}}
    <div class="zona-atas">

        {{-- <div class="logo-area">
            @php
                $logoPath = public_path('images/logo-ansor.png');
                $logoB64  = file_exists($logoPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                    : null;
            @endphp
            @if($logoB64)
                <img src="{{ $logoB64 }}" alt="Logo Ansor">
            @endif
            <div class="org-name">
                PIMPINAN CABANG<br>GERAKAN PEMUDA ANSOR<br>KABUPATEN KUDUS
            </div>
        </div> --}}

        <div class="title-sertifikat">SERTIFIKAT</div>

        <div class="subtitle">Diberikan kepada:</div>

        <div class="nama-anggota">{{ $anggota->nama_lengkap ?? '...' }}</div>

        <div class="info-anggota">
            {{ $anggota->nia ?? '' }}
            @if(!empty($anggota->nia) && !empty($anggota->alamat))<br>@endif
            {{ $anggota->alamat ?? '' }}
        </div>

        <div class="keterangan">
            Sebagai penghargaan atas partisipasinya dalam<br>
            <strong>{{ $pelatihan->nama_pelatihan ?? '...' }}</strong><br>
            yang berlangsung pada
            {{ $detail?->tanggal ? \Carbon\Carbon::parse($detail->tanggal)->translatedFormat('d F Y') : '...' }}
            di {{ $detail->tempat ?? '...' }}
        </div>

        <div class="penyelenggara">
            Pengurus Cabang<br>
            Gerakan Pemuda Ansor Kabupaten Kudus
        </div>

    </div>

    {{-- ZONA BAWAH: TTD --}}
    <div class="zona-ttd">

        <div class="ttd-box">
            @php
                $ttdKetuaB64 = null;
                if (!empty($template->ttd_ketua)) {
                    $p = storage_path('app/public/' . $template->ttd_ketua);
                    if (file_exists($p)) {
                        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION)) ?: 'png';
                        $ttdKetuaB64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }
            @endphp
            @if($ttdKetuaB64)
                <img class="ttd-img" src="{{ $ttdKetuaB64 }}" alt="TTD Ketua">
            @else
                <div class="ttd-space-fallback"></div>
            @endif
            <div class="ttd-line">
                <div class="ttd-nama">{{ $template->nama_ketua ?? '...' }}</div>
                <div class="ttd-jabatan">Ketua</div>
            </div>
        </div>

        <div class="ttd-box">
            @php
                $ttdSekB64 = null;
                if (!empty($template->ttd_sekretaris)) {
                    $p = storage_path('app/public/' . $template->ttd_sekretaris);
                    if (file_exists($p)) {
                        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION)) ?: 'png';
                        $ttdSekB64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($p));
                    }
                }
            @endphp
            @if($ttdSekB64)
                <img class="ttd-img" src="{{ $ttdSekB64 }}" alt="TTD Sekretaris">
            @else
                <div class="ttd-space-fallback"></div>
            @endif
            <div class="ttd-line">
                <div class="ttd-nama">{{ $template->nama_sekretaris ?? '...' }}</div>
                <div class="ttd-jabatan">Sekretaris</div>
            </div>
        </div>

    </div>

</div>

{{-- =================== HALAMAN BELAKANG =================== --}}
<div class="page-belakang">

{{--
    LAYOUT: outer table 3 baris
      row 1 = HEADER  : judul + data peserta
      row 2 = BODY    : tabel materi
      row 3 = FOOTER  : TTD instruktur | QR code
--}}
<table class="layout-table">
<tbody>

    {{-- ─── ROW 1: HEADER ─── --}}
    <tr>
        <td class="row-header">

            {{-- Judul kegiatan --}}
            <div class="belakang-title">
                {{ strtoupper($pelatihan->nama_pelatihan ?? 'Pelatihan') }}
            </div>

            {{-- Data peserta --}}
            <table class="info-table">
                <tr>
                    <td class="label-col">Nomor Registrasi</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $anggota->nomor_registrasi ?? $anggota->nia ?? '...' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Nama Peserta</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $anggota->nama_lengkap ?? '...' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tempat, Tanggal Lahir</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        {{ $anggota->tempat_lahir ?? '...' }},
                        {{ $anggota->tanggal_lahir
                            ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->translatedFormat('d F Y')
                            : '...' }}
                    </td>
                </tr>
                <tr>
                    <td class="label-col">Utusan</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ optional($anggota->kecamatan)->nama_kecamatan ?? $anggota->kecamatan ?? '...' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Alamat</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $anggota->alamat_lengkap ?? '...' }}</td>
                </tr>
            </table>

            <hr class="header-divider">

        </td>
    </tr>

    {{-- ─── ROW 2: BODY — tabel materi ─── --}}
    <tr>
        <td class="row-body">

            <table class="materi-table">
                <colgroup>
                    <col style="width:5%">
                    <col style="width:47%">
                    <col style="width:16%">
                    <col style="width:32%">
                </colgroup>
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>MATERI {{ strtoupper($pelatihan->nama_pelatihan ?? 'PELATIHAN') }}</th>
                        <th>DURASI</th>
                        <th>INSTRUKTUR</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalMenit = 0; $rowNum = 0; @endphp
                    @forelse($materiList as $index => $sesi)
                        @php
                            $menit = 0;
                            if ($sesi->jam_mulai && $sesi->jam_selesai) {
                                $menit = abs(\Carbon\Carbon::parse($sesi->jam_mulai)
                                    ->diffInMinutes(\Carbon\Carbon::parse($sesi->jam_selesai)));
                            }
                            $totalMenit += $menit;
                            $rowNum++;
                            $rc = ($rowNum % 2 === 0) ? 'even' : '';
                        @endphp
                        <tr class="{{ $rc }}">
                            <td class="col-no">{{ $index + 1 }}</td>
                            <td>{{ $sesi->materi->nama_materi ?? '-' }}</td>
                            <td class="col-durasi">{{ $menit > 0 ? $menit . ' Menit' : '—' }}</td>
                            <td>{{ $sesi->pengajar ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:8px; color:#999; font-size:10px;">
                                Tidak ada data materi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right;">TOTAL DURASI</td>
                        <td class="col-durasi">{{ $totalMenit > 0 ? $totalMenit . ' Menit' : '—' }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

        </td>
    </tr>

    {{-- ─── ROW 3: FOOTER — TTD + QR ─── --}}
    <tr>
        <td class="row-footer">

            <table class="footer-table">
                <tr>
                    {{-- TTD Instruktur (kiri) --}}
                    <td class="footer-ttd" Style="padding-top: 10px;">
                        @if($detail)
                        <div class="ttd-instruktur">
                            <div class="tim-title">
                                DEWAN INSTRUKTUR<br>
                                {{ strtoupper($pelatihan->nama_pelatihan ?? 'PELATIHAN') }}<br>
                                PIMPINAN CABANG GP ANSOR KABUPATEN KUDUS
                            </div>
                            <div style="margin-top:5px; font-size:10px; color:#555;">Instruktur Cabang,</div>
                            <span class="ttd-space"></span>
                            <span class="nama-ttd">{{ $template->nama_instruktur ?? $detail->pengajar ?? '...' }}</span>
                            <div class="jabatan-ttd">Instruktur</div>
                        </div>
                        @endif
                    </td>

                    {{-- QR Code (kanan) --}}
                    <td class="footer-qr" style="padding-top: 10px;">
                        @php
                            $nia      = $anggota->nia ?? $anggota->nomor_registrasi ?? '';
                            $qrUrl    = url('/verifikasi/sertifikat/' . urlencode($nia));
                            $qrImgSrc = $qrBase64
                                ?? ('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrUrl));
                        @endphp
                        @if(!empty($nia))
                        <div class="qr-area">
                            <div class="qr-box">
                                <img src="{{ $qrImgSrc }}" alt="QR Verifikasi">
                            </div>
                            <div class="qr-label">
                                Scan untuk verifikasi<br>
                                <strong>NIA: {{ $nia }}</strong>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
            </table>

        </td>
    </tr>

</tbody>
</table>

</div>

</body>
</html>