<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <x-heroicon-o-calendar style="width:1.125rem; height:1.125rem; color:#059669;" />
                <span style="font-weight:700;">Pelatihan Terdekat</span>
            </div>
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::button
                href="{{ url('/admin/daftar-pelatihan') }}"
                tag="a"
                size="sm"
                icon="heroicon-o-user-plus"
                color="primary"
            >
                Ikuti Pelatihan
            </x-filament::button>
        </x-slot>

        @if ($pelatihanList->isEmpty())
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:2.5rem 1rem; text-align:center; color:#9ca3af;">
                <x-heroicon-o-calendar style="width:2.5rem; height:2.5rem; margin-bottom:0.75rem; opacity:0.4;" />
                <p style="font-size:0.875rem; font-weight:600;">Belum ada pelatihan yang dijadwalkan</p>
                <p style="font-size:0.75rem; margin-top:0.25rem;">Pantau terus halaman ini untuk info terbaru</p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:0.875rem;">
                @foreach ($pelatihanList as $pelatihan)
                    @php
                        $sesiList    = $pelatihan->sesiAktif;
                        $tglAwal     = $sesiList->first()?->tanggal ? \Carbon\Carbon::parse($sesiList->first()->tanggal) : null;
                        $tglAkhir    = $sesiList->last()?->tanggal  ? \Carbon\Carbon::parse($sesiList->last()->tanggal)  : null;

                        $diffDays    = $tglAwal ? (int) now()->startOfDay()->diffInDays($tglAwal->copy()->startOfDay()) : null;
                        $isToday     = $tglAwal?->isToday();
                        $isTomorrow  = $tglAwal?->isTomorrow();

                        $badgeBg     = match(true) {
                            $isToday       => '#fee2e2', $isTomorrow => '#ffedd5',
                            $diffDays <= 7 => '#fef9c3', default     => '#dbeafe',
                        };
                        $badgeText   = match(true) {
                            $isToday       => '#9f1239', $isTomorrow => '#9a3412',
                            $diffDays <= 7 => '#854d0e', default     => '#1e40af',
                        };
                        $badgeLabel  = match(true) {
                            $isToday        => 'Hari Ini',
                            $isTomorrow     => 'Besok',
                            $diffDays !== null && $diffDays <= 7 => $diffDays . ' hari lagi',
                            $tglAwal !== null => $tglAwal->translatedFormat('d M Y'),
                            default         => '—',
                        };

                        $tempatList   = $sesiList->pluck('tempat')->filter()->unique()->values();
                        $pengajarList = $sesiList->pluck('pengajar')->filter()->unique()->values();
                        $totalSesi    = $sesiList->count();
                    @endphp

                    <div style="border-radius:1rem; overflow:hidden; border:1.5px solid #d1fae5; background:#fff;">

                        {{-- Header card --}}
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; padding:0.875rem 1rem; background:linear-gradient(135deg,#064e3b,#059669 65%,#34d399); flex-wrap:wrap;">
                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.25rem;">
                                    <span style="font-size:0.9375rem; font-weight:800; color:#fff; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $pelatihan->nama_pelatihan }}
                                    </span>
                                    <span style="flex-shrink:0; border-radius:9999px; padding:0.15rem 0.5rem; font-size:0.65rem; font-weight:700; background:{{ $badgeBg }}; color:{{ $badgeText }};">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>
                                <div style="display:flex; flex-wrap:wrap; gap:0.5rem; font-size:0.7rem; color:rgba(255,255,255,0.85);">
                                    <span style="display:flex; align-items:center; gap:0.2rem;">
                                        <x-heroicon-o-rectangle-stack style="width:0.75rem; height:0.75rem;" />
                                        {{ $totalSesi }} sesi
                                    </span>
                                    @if ($tglAwal)
                                        <span style="display:flex; align-items:center; gap:0.2rem;">
                                            <x-heroicon-o-calendar style="width:0.75rem; height:0.75rem;" />
                                            {{ $tglAwal->translatedFormat('d M Y') }}
                                            @if ($tglAkhir && $tglAkhir->ne($tglAwal))
                                                — {{ $tglAkhir->translatedFormat('d M Y') }}
                                            @endif
                                        </span>
                                    @endif
                                    @if ($tempatList->isNotEmpty())
                                        <span style="display:flex; align-items:center; gap:0.2rem;">
                                            <x-heroicon-o-map-pin style="width:0.75rem; height:0.75rem;" />
                                            {{ $tempatList->join(' · ') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ url('/admin/daftar-pelatihan') }}"
                               style="flex-shrink:0; display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.625rem; padding:0.4rem 0.875rem; font-size:0.75rem; font-weight:700; color:#059669; background:#fff; border:none; text-decoration:none; white-space:nowrap; box-shadow:0 1px 4px rgba(0,0,0,0.1); transition:opacity 0.15s;"
                               onmouseover="this.style.background='#d1fae5';" onmouseout="this.style.background='#fff';">
                                <x-heroicon-o-arrow-right style="width:0.75rem; height:0.75rem;" />
                                Ikuti
                            </a>
                        </div>

                        {{-- Daftar sesi (maks 3 tampil, sisanya dilipat) --}}
                        <div style="padding:0.5rem 0.75rem;">
                            @foreach ($sesiList->take(3) as $idx => $sesi)
                                @php $tSesi = \Carbon\Carbon::parse($sesi->tanggal); @endphp
                                <div style="display:flex; align-items:center; gap:0.625rem; padding:0.4rem 0.25rem; {{ $idx > 0 ? 'border-top:1px solid #f3f4f6;' : '' }}">
                                    {{-- Nomor --}}
                                    <span style="flex-shrink:0; display:flex; align-items:center; justify-content:center; width:1.25rem; height:1.25rem; border-radius:0.3rem; background:{{ $tSesi->isToday() ? '#d1fae5' : '#f3f4f6' }}; font-size:0.6rem; font-weight:800; color:{{ $tSesi->isToday() ? '#065f46' : '#6b7280' }};">
                                        {{ $idx + 1 }}
                                    </span>
                                    {{-- Tanggal --}}
                                    <span style="flex-shrink:0; min-width:4.5rem; font-size:0.75rem; font-weight:700; color:#374151;">
                                        {{ $tSesi->translatedFormat('d M') }}
                                    </span>
                                    {{-- Materi --}}
                                    <span style="flex:1; font-size:0.75rem; color:#6b7280; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $sesi->materi?->nama_materi ?? '—' }}
                                    </span>
                                    {{-- Jam --}}
                                    @if ($sesi->jam_mulai && $sesi->jam_selesai)
                                        <span style="flex-shrink:0; font-size:0.7rem; color:#9ca3af; white-space:nowrap;">
                                            {{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($sesi->jam_selesai)->format('H:i') }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach

                            @if ($sesiList->count() > 3)
                                <div style="padding:0.375rem 0.25rem; border-top:1px solid #f3f4f6; text-align:center;">
                                    <a href="{{ url('/admin/daftar-pelatihan') }}"
                                       style="font-size:0.7rem; color:#059669; font-weight:600; text-decoration:none;">
                                        +{{ $sesiList->count() - 3 }} sesi lainnya →
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Footer instruktur --}}
                        @if ($pengajarList->isNotEmpty())
                            <div style="display:flex; align-items:center; gap:0.375rem; padding:0.5rem 1rem; background:#f0fdf4; border-top:1px solid #d1fae5; flex-wrap:wrap;">
                                <span style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9ca3af;">Instruktur:</span>
                                @foreach ($pengajarList as $pgj)
                                    <span style="display:inline-flex; align-items:center; gap:0.2rem; border-radius:9999px; padding:0.15rem 0.5rem; font-size:0.7rem; font-weight:600; background:#d1fae5; color:#065f46; border:1px solid #6ee7b7;">
                                        {{ $pgj }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            <div style="margin-top:0.875rem; text-align:center;">
                <a href="{{ url('/admin/daftar-pelatihan') }}"
                   style="font-size:0.8125rem; color:#059669; font-weight:600; text-decoration:none;"
                   onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
                    Lihat semua pelatihan →
                </a>
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>