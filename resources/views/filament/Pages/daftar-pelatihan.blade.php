<x-filament-panels::page>

    {{-- ================================================================
         DAFTAR PELATIHAN — tampil per PELATIHAN, materi di bawahnya
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
                    $materiList = $pelatihan->pelatihanDetails;

                    $tglAwal  = $pelatihan->tanggal_mulai ? \Carbon\Carbon::parse($pelatihan->tanggal_mulai) : null;
                    $tglAkhir = $pelatihan->tanggal_selesai ? \Carbon\Carbon::parse($pelatihan->tanggal_selesai) : $tglAwal;

                    $sedangBerlangsung = $tglAwal && $tglAkhir
                        && $tglAwal->copy()->startOfDay()->lte(now())
                        && $tglAkhir->copy()->endOfDay()->gte(now());

                    $diffDays   = $tglAwal ? (int) now()->startOfDay()->diffInDays($tglAwal->copy()->startOfDay(), false) : null;
                    $isTomorrow = $tglAwal?->isTomorrow();

                    $badgeColor = match(true) {
                        $sedangBerlangsung => ['bg' => '#d1fae5', 'text' => '#065f46', 'border' => '#6ee7b7'],
                        $isTomorrow        => ['bg' => '#ffedd5', 'text' => '#9a3412', 'border' => '#fdba74'],
                        $diffDays !== null && $diffDays <= 7 => ['bg' => '#fef9c3', 'text' => '#854d0e', 'border' => '#fcd34d'],
                        default            => ['bg' => '#dbeafe', 'text' => '#1e40af', 'border' => '#93c5fd'],
                    };

                    $badgeLabel = match(true) {
                        $sedangBerlangsung => 'Sedang Berlangsung',
                        $isTomorrow        => 'Besok',
                        $diffDays !== null && $diffDays <= 7 => $diffDays . ' hari lagi',
                        $tglAwal !== null  => $tglAwal->translatedFormat('d M Y'),
                        default            => '—',
                    };

                    // Pengajar unik dari seluruh materi
                    $pengajarList = $materiList->pluck('pengajar')->filter()->unique()->values();
                    $totalMateri  = $materiList->count();
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
                                    <h2 style="font-size:1rem; font-weight:900; color:#fff; line-height:1.2; word-break:break-word;">
                                        {{ $pelatihan->nama_pelatihan }}
                                    </h2>
                                    <span style="flex-shrink:0; display:inline-flex; align-items:center; border-radius:9999px; padding:0.2rem 0.625rem; font-size:0.7rem; font-weight:700; background:{{ $badgeColor['bg'] }}; color:{{ $badgeColor['text'] }}; border:1px solid {{ $badgeColor['border'] }};">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>

                                {{-- Meta: jumlah materi, rentang tanggal, tempat --}}
                                <div style="display:flex; flex-wrap:wrap; gap:0.75rem; font-size:0.75rem; color:rgba(255,255,255,0.85);">
                                    <span style="display:flex; align-items:center; gap:0.3rem;">
                                        <x-heroicon-o-rectangle-stack style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                                        {{ $totalMateri }} materi
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
                                    @if ($pelatihan->tempat)
                                        <span style="display:flex; align-items:center; gap:0.3rem;">
                                            <x-heroicon-o-map-pin style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                                            {{ $pelatihan->tempat }}
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

                    {{-- ── DESKRIPSI ── --}}
                    @if ($pelatihan->deskripsi)
                        <div style="padding:0.75rem 1.5rem; background:#f0fdf4; border-bottom:1px solid #d1fae5; font-size:0.8125rem; color:#374151;">
                            {{ $pelatihan->deskripsi }}
                        </div>
                    @endif

                    {{-- ── DAFTAR MATERI ── --}}
                    <div style="padding:0.75rem 1rem;">
                        <p style="font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.5rem; padding-left:0.5rem;">
                            Materi Pelatihan ({{ $totalMateri }} materi)
                        </p>

                        <div style="display:flex; flex-direction:column; gap:0.375rem;">
                            @foreach ($materiList as $idx => $detail)
                                <div style="display:flex; align-items:center; gap:0.875rem; padding:0.625rem 0.75rem; border-radius:0.75rem; background:#f9fafb; border:1px solid #f3f4f6; flex-wrap:wrap;">

                                    {{-- Nomor --}}
                                    <span style="flex-shrink:0; display:flex; align-items:center; justify-content:center; width:1.5rem; height:1.5rem; border-radius:0.375rem; background:#e5e7eb; font-size:0.7rem; font-weight:800; color:#6b7280;">
                                        {{ $idx + 1 }}
                                    </span>

                                    {{-- Nama materi --}}
                                    <span style="flex:1; min-width:10rem; font-size:0.8125rem; color:#1f2937; font-weight:600; word-break:break-word;">
                                        {{ $detail->materi?->nama_materi ?? '—' }}
                                    </span>

                                    {{-- Pengajar --}}
                                    @if ($detail->pengajar)
                                        <span style="flex-shrink:0; display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#6b7280;">
                                            <x-heroicon-o-user style="width:0.75rem; height:0.75rem; flex-shrink:0;" />
                                            {{ $detail->pengajar }}
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