<x-filament-panels::page>

    {{-- ================================================================
         DAFTAR PELATIHAN — tampil per PELATIHAN, sesi/materi di bawahnya
         ================================================================ --}}

    @php $pelatihanList = $this->getPelatihanList(); @endphp

    @if ($pelatihanList->isEmpty())
        <x-filament::section>
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:4rem 2rem; text-align:center; color:#9ca3af;">
                <x-heroicon-o-calendar style="width:3.5rem; height:3.5rem; margin-bottom:1rem; opacity:0.4;" />
                <p style="font-size:1rem; font-weight:600;">Belum ada jadwal pelatihan</p>
                <p style="font-size:0.875rem; margin-top:0.25rem;">Silakan cek kembali nanti.</p>
            </div>
        </x-filament::section>
    @else
        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            @foreach ($pelatihanList as $pelatihan)
                @php
                    $sesiList   = $pelatihan->sesiAktif;
                    $tanggalAwal = $sesiList->first()?->tanggal;
                    $tanggalAkhir = $sesiList->last()?->tanggal;

                    $tglAwal = $tanggalAwal ? \Carbon\Carbon::parse($tanggalAwal) : null;
                    $tglAkhir = $tanggalAkhir ? \Carbon\Carbon::parse($tanggalAkhir) : null;

                    $diffDays = $tglAwal ? (int) now()->startOfDay()->diffInDays($tglAwal->copy()->startOfDay()) : null;
                    $isToday  = $tglAwal?->isToday();
                    $isTomorrow = $tglAwal?->isTomorrow();

                    $badgeColor = match(true) {
                        $isToday           => ['bg' => '#fee2e2', 'text' => '#9f1239', 'border' => '#fca5a5'],
                        $isTomorrow        => ['bg' => '#ffedd5', 'text' => '#9a3412', 'border' => '#fdba74'],
                        $diffDays <= 7     => ['bg' => '#fef9c3', 'text' => '#854d0e', 'border' => '#fcd34d'],
                        default            => ['bg' => '#dbeafe', 'text' => '#1e40af', 'border' => '#93c5fd'],
                    };

                    $badgeLabel = match(true) {
                        $isToday           => 'Hari Ini',
                        $isTomorrow        => 'Besok',
                        $diffDays !== null && $diffDays <= 7 => $diffDays . ' hari lagi',
                        $tglAwal !== null   => $tglAwal->translatedFormat('d M Y'),
                        default            => '—',
                    };

                    // Kumpulkan tempat unik
                    $tempatList = $sesiList->pluck('tempat')->filter()->unique()->values();
                    // Kumpulkan pengajar unik
                    $pengajarList = $sesiList->pluck('pengajar')->filter()->unique()->values();
                    // Total sesi
                    $totalSesi = $sesiList->count();
                    // Total durasi menit
                    $totalMenit = $sesiList->sum(function ($s) {
                        if ($s->jam_mulai && $s->jam_selesai) {
                            return abs(\Carbon\Carbon::parse($s->jam_mulai)->diffInMinutes(\Carbon\Carbon::parse($s->jam_selesai)));
                        }
                        return 0;
                    });
                @endphp

                <div style="border-radius:1.25rem; background:#fff; border:1.5px solid #a7f3d0; box-shadow:0 2px 8px rgba(5,150,105,0.07); overflow:hidden;">

                    {{-- ── HEADER PELATIHAN ── --}}
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1.25rem 1.5rem; background:linear-gradient(135deg, #064e3b 0%, #059669 60%, #34d399 100%); flex-wrap:wrap;">

                        <div style="display:flex; align-items:center; gap:1rem; flex:1; min-width:0;">
                            {{-- Ikon pelatihan --}}
                            <div style="flex-shrink:0; display:flex; align-items:center; justify-content:center; width:3rem; height:3rem; border-radius:0.875rem; background:rgba(255,255,255,0.15); border:1.5px solid rgba(255,255,255,0.25);">
                                <x-heroicon-o-academic-cap style="width:1.5rem; height:1.5rem; color:#fff;" />
                            </div>

                            <div style="min-width:0;">
                                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.25rem;">
                                    <h2 style="font-size:1rem; font-weight:900; color:#fff; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $pelatihan->nama_pelatihan }}
                                    </h2>
                                    <span style="flex-shrink:0; display:inline-flex; align-items:center; border-radius:9999px; padding:0.2rem 0.625rem; font-size:0.7rem; font-weight:700; background:{{ $badgeColor['bg'] }}; color:{{ $badgeColor['text'] }}; border:1px solid {{ $badgeColor['border'] }};">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>

                                {{-- Meta: total sesi, tanggal range, tempat --}}
                                <div style="display:flex; flex-wrap:wrap; gap:0.75rem; font-size:0.75rem; color:rgba(255,255,255,0.85);">
                                    <span style="display:flex; align-items:center; gap:0.3rem;">
                                        <x-heroicon-o-rectangle-stack style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                                        {{ $totalSesi }} sesi
                                    </span>
                                    @if ($tglAwal)
                                        <span style="display:flex; align-items:center; gap:0.3rem;">
                                            <x-heroicon-o-calendar style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                                            {{ $tglAwal->translatedFormat('d M Y') }}
                                            @if ($tglAkhir && $tglAkhir->ne($tglAwal))
                                                — {{ $tglAkhir->translatedFormat('d M Y') }}
                                            @endif
                                        </span>
                                    @endif
                                    @if ($tempatList->isNotEmpty())
                                        <span style="display:flex; align-items:center; gap:0.3rem;">
                                            <x-heroicon-o-map-pin style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                                            {{ $tempatList->join(' · ') }}
                                        </span>
                                    @endif
                                    @if ($totalMenit > 0)
                                        <span style="display:flex; align-items:center; gap:0.3rem;">
                                            <x-heroicon-o-clock style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                                            {{ $totalMenit }} menit total
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Ikuti --}}
                        <button
                            wire:click="ikutiPelatihan({{ $pelatihan->id }})"
                            style="flex-shrink:0; display:inline-flex; align-items:center; gap:0.5rem; border-radius:0.875rem; padding:0.625rem 1.25rem; font-size:0.875rem; font-weight:700; color:#059669; background:#fff; border:none; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); transition:all 0.15s; white-space:nowrap;"
                            onmouseover="this.style.background='#d1fae5'; this.style.transform='translateY(-1px)';"
                            onmouseout="this.style.background='#fff'; this.style.transform='translateY(0)';">
                            <x-heroicon-o-user-plus style="width:1rem; height:1rem;" />
                            Ikuti Pelatihan
                        </button>
                    </div>

                    {{-- ── DAFTAR SESI / MATERI ── --}}
                    @if ($pelatihan->deskripsi)
                        <div style="padding:0.75rem 1.5rem; background:#f0fdf4; border-bottom:1px solid #d1fae5; font-size:0.8125rem; color:#374151;">
                            {{ $pelatihan->deskripsi }}
                        </div>
                    @endif

                    <div style="padding:0.75rem 1rem;">
                        <p style="font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.5rem; padding-left:0.5rem;">
                            Jadwal Sesi ({{ $totalSesi }} sesi)
                        </p>

                        <div style="display:flex; flex-direction:column; gap:0.375rem;">
                            @foreach ($sesiList as $idx => $sesi)
                                @php
                                    $tSesi = \Carbon\Carbon::parse($sesi->tanggal);
                                    $isSesiToday = $tSesi->isToday();
                                @endphp
                                <div style="display:flex; align-items:center; gap:0.875rem; padding:0.625rem 0.75rem; border-radius:0.75rem; background:{{ $isSesiToday ? '#ecfdf5' : '#f9fafb' }}; border:1px solid {{ $isSesiToday ? '#6ee7b7' : '#f3f4f6' }};">

                                    {{-- Nomor sesi --}}
                                    <span style="flex-shrink:0; display:flex; align-items:center; justify-content:center; width:1.5rem; height:1.5rem; border-radius:0.375rem; background:{{ $isSesiToday ? '#d1fae5' : '#e5e7eb' }}; font-size:0.7rem; font-weight:800; color:{{ $isSesiToday ? '#065f46' : '#6b7280' }};">
                                        {{ $idx + 1 }}
                                    </span>

                                    {{-- Tanggal --}}
                                    <span style="flex-shrink:0; min-width:5.5rem; font-size:0.8125rem; font-weight:700; color:#1f2937;">
                                        {{ $tSesi->translatedFormat('d M Y') }}
                                        @if ($isSesiToday)
                                            <span style="display:inline; font-size:0.6rem; font-weight:700; background:#d1fae5; color:#065f46; border-radius:9999px; padding:0.1rem 0.375rem; margin-left:0.25rem;">Hari ini</span>
                                        @endif
                                    </span>

                                    {{-- Materi --}}
                                    <span style="flex:1; font-size:0.8125rem; color:#374151; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $sesi->materi?->nama_materi ?? '—' }}
                                    </span>

                                    {{-- Waktu --}}
                                    @if ($sesi->jam_mulai && $sesi->jam_selesai)
                                        <span style="flex-shrink:0; display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#9ca3af; white-space:nowrap;">
                                            <x-heroicon-o-clock style="width:0.75rem; height:0.75rem;" />
                                            {{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($sesi->jam_selesai)->format('H:i') }}
                                        </span>
                                    @endif

                                    {{-- Tempat --}}
                                    @if ($sesi->tempat)
                                        <span style="flex-shrink:0; display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#9ca3af; white-space:nowrap;">
                                            <x-heroicon-o-map-pin style="width:0.75rem; height:0.75rem;" />
                                            {{ $sesi->tempat }}
                                        </span>
                                    @endif

                                    {{-- Pengajar --}}
                                    @if ($sesi->pengajar)
                                        <span style="flex-shrink:0; display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#9ca3af; max-width:10rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            <x-heroicon-o-user style="width:0.75rem; height:0.75rem; flex-shrink:0;" />
                                            {{ $sesi->pengajar }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── FOOTER: Instruktur unik ── --}}
                    @if ($pengajarList->isNotEmpty())
                        <div style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1.5rem; background:#f0fdf4; border-top:1px solid #d1fae5; flex-wrap:wrap;">
                            <span style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af;">Instruktur:</span>
                            @foreach ($pengajarList as $pgj)
                                <span style="display:inline-flex; align-items:center; gap:0.25rem; border-radius:9999px; padding:0.2rem 0.625rem; font-size:0.75rem; font-weight:600; background:#d1fae5; color:#065f46; border:1px solid #6ee7b7;">
                                    <x-heroicon-o-user style="width:0.75rem; height:0.75rem;" />
                                    {{ $pgj }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                </div>{{-- end card pelatihan --}}
            @endforeach
        </div>
    @endif


  {{-- ================================================================
         MODAL: Input NIK / NIA + Nama + Tanggal Lahir untuk verifikasi
         ================================================================ --}}
    <x-filament::modal id="cari-anggota-modal" width="md">

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <x-heroicon-o-shield-check style="width:1.25rem; height:1.25rem; color:#059669;" />
                Verifikasi Identitas
            </div>
        </x-slot>

        <x-slot name="description">
            Masukkan <strong>NIK/NIA</strong>, <strong>nama lengkap</strong>, dan <strong>tanggal lahir</strong>
            sesuai data pendaftaran Anda untuk melanjutkan. Ketiga data harus cocok.
        </x-slot>

        <form wire:submit.prevent="prosesVerifikasi">
            {{ $this->form }}

            <div style="display:flex; justify-content:flex-end; gap:0.625rem; margin-top:1.25rem;">
                <x-filament::button
                    type="button"
                    color="gray"
                    x-on:click="$dispatch('close-modal', { id: 'cari-anggota-modal' })"
                >
                    Batal
                </x-filament::button>

                <x-filament::button
                    type="submit"
                    color="primary"
                    icon="heroicon-o-arrow-right"
                    icon-position="after"
                >
                    Verifikasi & Daftar
                </x-filament::button>
            </div>
        </form>

    </x-filament::modal>

</x-filament-panels::page>