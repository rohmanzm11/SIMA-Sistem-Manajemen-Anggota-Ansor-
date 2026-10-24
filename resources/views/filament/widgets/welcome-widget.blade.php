@php
    [
        'user'     => $user,
        'anggota'  => $anggota,
        'fotoUrl'  => $fotoUrl,
        'role'     => $role,
        'greeting' => $greeting,
    ] = $this->getViewData();

    $inisial = strtoupper(substr($user->name, 0, 2));
@endphp

<x-filament-widgets::widget>
<div
    class="relative overflow-hidden rounded-2xl shadow-lg"
    style="background: linear-gradient(135deg, #064e3b 0%, #059669 55%, #34d399 100%); padding: 0;"
>
    {{-- Dekorasi lingkaran --}}
    <div style="pointer-events:none;position:absolute;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,0.05);top:-80px;right:-80px;"></div>
    <div style="pointer-events:none;position:absolute;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-60px;left:-40px;"></div>
    <div style="pointer-events:none;position:absolute;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.05);bottom:20px;right:300px;"></div>

    {{-- KONTEN UTAMA --}}
    <div style="position:relative; display:flex; align-items:stretch; gap:0;">

        {{-- ── Foto ─────────────────────────────────────────────────── --}}
        <div style="padding: 20px 0 20px 20px; display:flex; align-items:center; flex-shrink:0;">
            @if ($fotoUrl)
                <img
                    src="{{ $fotoUrl }}"
                    alt="{{ $user->name }}"
                    style="width:110px;height:110px;border-radius:16px;object-fit:cover;object-position:top;border:3px solid rgba(255,255,255,0.5);box-shadow:0 8px 24px rgba(0,0,0,0.25);"
                />
            @else
                <div style="width:110px;height:110px;border-radius:16px;background:rgba(255,255,255,0.15);border:3px solid rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(0,0,0,0.2);">
                    <span style="color:white;font-size:2rem;font-weight:800;letter-spacing:2px;">{{ $inisial }}</span>
                </div>
            @endif
        </div>

        {{-- ── Info Nama & Badge ────────────────────────────────────── --}}
        <div style="padding: 20px 24px; flex:1; display:flex; flex-direction:column; justify-content:center; min-width:0;">
            <p style="color:rgba(255,255,255,0.65);font-size:0.8rem;font-weight:500;margin:0 0 2px 0;">
                {{ $greeting }},
            </p>
            <h2 style="color:white;font-size:1.35rem;font-weight:800;margin:0 0 10px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;letter-spacing:0.3px;">
                {{ $anggota?->nama_lengkap ?? $user->name }}
            </h2>

            {{-- Badges --}}
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;background:rgba(255,255,255,0.15);color:white;font-size:0.72rem;font-weight:600;backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.2);">
                    🔐 {{ $role }}
                </span>
                @if ($anggota?->kecamatan)
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;background:rgba(255,255,255,0.15);color:white;font-size:0.72rem;font-weight:600;border:1px solid rgba(255,255,255,0.2);">
                    📍 {{ $anggota->kecamatan->nama_kecamatan }}
                </span>
                @endif
                @if ($anggota?->nia)
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;background:rgba(255,255,255,0.15);color:white;font-size:0.7rem;font-family:monospace;border:1px solid rgba(255,255,255,0.2);">
                    🪪 {{ $anggota->nia }}
                </span>
                @endif
            </div>
        </div>

        {{-- ── DIVIDER VERTIKAL ────────────────────────────────────── --}}
        <div style="width:1px;background:rgba(255,255,255,0.15);margin:20px 0;"></div>

        {{-- ── Waktu, Tanggal & Tombol ─────────────────────────────── --}}
        <div style="padding: 20px 28px; display:flex; flex-direction:column; align-items:flex-end; justify-content:center; flex-shrink:0; gap:4px;">
            <span style="color:rgba(255,255,255,0.5);font-size:0.75rem;">{{ now()->format('H:i') }} WIB</span>
            <span style="color:white;font-size:1rem;font-weight:700;">{{ now()->translatedFormat('d F Y') }}</span>
            <span style="color:rgba(255,255,255,0.5);font-size:0.75rem;margin-bottom:10px;">{{ now()->translatedFormat('l') }}</span>

            {{-- Tombol Dashboard Anggota --}}
            @if ($anggota)
                <a
                    href="{{ \App\Filament\Pages\DashboardAnggota::getUrl(['anggotaId' => $anggota->id]) }}"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:99px;background:rgba(255,255,255,0.2);color:white;font-size:0.75rem;font-weight:700;text-decoration:none;border:1px solid rgba(255,255,255,0.35);backdrop-filter:blur(4px);white-space:nowrap;cursor:pointer;"
                    onmouseover="this.style.background='rgba(255,255,255,0.32)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.2)'"
                >
                    👤 Dashboard Saya
                </a>
            @endif
        </div>

    </div>
</div>
</x-filament-widgets::widget>