<x-filament-panels::page>
    {{-- Tailwind via script. Prefix "tw-" + preflight dimatikan agar tidak bentrok dengan CSS Filament --}}
    <script src="https://cdn.tailwindcss.com/3.4.17" data-navigate-once></script>
    <script data-navigate-once>
        const shades = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];
        const palette = (name) => Object.fromEntries(shades.map((s) => [s, `rgb(var(--${name}-${s}))`]));

        tailwind.config = {
            prefix: 'tw-',
            darkMode: 'class',
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    // Warna mengikuti tema Filament (primary / success / danger)
                    colors: {
                        primary: palette('primary'),
                        success: palette('success'),
                        danger: palette('danger'),
                    },
                },
            },
        };
    </script>

    @php
        $stats        = $this->getPelatihanStats();
        $terpilih     = $stats->firstWhere('id', $this->selectedPelatihanId);
        $totalPeserta = $stats->sum('peserta');
        $totalLulus   = $stats->sum('lulus');
        $totalDitolak = $stats->sum('ditolak');
        $persenTotal  = $totalPeserta > 0 ? round($totalLulus / $totalPeserta * 100) : 0;
    @endphp

    @if ($stats->isEmpty())
        <div class="tw-rounded-2xl tw-border-2 tw-border-dashed tw-border-gray-300 tw-p-10 tw-text-center dark:tw-border-white/20">
            <p class="tw-text-base tw-font-medium tw-text-gray-900 dark:tw-text-white">Belum ada pelatihan</p>
            <p class="tw-mt-1 tw-text-sm tw-text-gray-500 dark:tw-text-gray-400">
                Klik <strong>Input Pelatihan</strong> di kanan atas untuk menambahkan pelatihan pertama.
            </p>
        </div>
    @else
        {{-- ============ RINGKASAN KESELURUHAN ============ --}}
        <div class="tw-relative tw-overflow-hidden tw-rounded-3xl tw-bg-gradient-to-br tw-from-primary-500 tw-via-primary-600 tw-to-primary-900 tw-p-6 tw-text-white tw-shadow-lg sm:tw-p-8">
            <div class="tw-pointer-events-none tw-absolute -tw-right-16 -tw-top-16 tw-h-64 tw-w-64 tw-rounded-full tw-bg-white/10"></div>
            <div class="tw-pointer-events-none tw-absolute -tw-bottom-24 tw-right-40 tw-h-56 tw-w-56 tw-rounded-full tw-bg-white/5"></div>

            <div class="tw-relative tw-flex tw-flex-col tw-gap-6 lg:tw-flex-row lg:tw-items-end lg:tw-justify-between">
                <div>
                    <p class="tw-text-sm tw-text-white/80">Total pelatihan</p>
                    <p class="tw-mt-1 tw-text-5xl tw-font-bold tw-leading-none">{{ $stats->count() }}</p>
                    <p class="tw-mt-3 tw-max-w-md tw-text-sm tw-text-white/80">
                        {{ $persenTotal }}% peserta dinyatakan lulus. Klik kartu pelatihan di bawah untuk melihat daftar pesertanya.
                    </p>
                </div>

                <div class="tw-grid tw-grid-cols-3 tw-gap-3 sm:tw-gap-4">
                    <div class="tw-rounded-2xl tw-bg-white/15 tw-px-4 tw-py-3 tw-backdrop-blur">
                        <p class="tw-text-2xl tw-font-semibold">{{ $totalPeserta }}</p>
                        <p class="tw-text-xs tw-text-white/80">Peserta</p>
                    </div>
                    <div class="tw-rounded-2xl tw-bg-white/15 tw-px-4 tw-py-3 tw-backdrop-blur">
                        <p class="tw-text-2xl tw-font-semibold">{{ $totalLulus }}</p>
                        <p class="tw-text-xs tw-text-white/80">Lulus</p>
                    </div>
                    <div class="tw-rounded-2xl tw-bg-white/15 tw-px-4 tw-py-3 tw-backdrop-blur">
                        <p class="tw-text-2xl tw-font-semibold">{{ $totalDitolak }}</p>
                        <p class="tw-text-xs tw-text-white/80">Belum memenuhi syarat</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ KARTU PER PELATIHAN ============ --}}
        <div class="tw-grid tw-grid-cols-1 tw-gap-5 md:tw-grid-cols-2 xl:tw-grid-cols-3">
            @foreach ($stats as $s)
                @php
                    $aktif     = $this->selectedPelatihanId === $s['id'];
                    $pctLulus  = $s['peserta'] > 0 ? round($s['lulus'] / $s['peserta'] * 100) : 0;
                    $pctDitolak = $s['peserta'] > 0 ? 100 - $pctLulus : 0;
                @endphp

                <button
                    type="button"
                    wire:key="kartu-{{ $s['id'] }}"
                    wire:click="pilihPelatihan({{ $s['id'] }})"
                    x-data
                    x-on:click="setTimeout(() => document.getElementById('panel-peserta')?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 350)"
                    class="tw-group tw-flex tw-flex-col tw-overflow-hidden tw-rounded-2xl tw-bg-white tw-text-left tw-shadow-sm tw-transition tw-duration-200 hover:-tw-translate-y-1 hover:tw-shadow-xl dark:tw-bg-gray-900
                        {{ $aktif
                            ? 'tw-ring-2 tw-ring-primary-500 tw-shadow-lg'
                            : 'tw-ring-1 tw-ring-gray-950/5 dark:tw-ring-white/10' }}"
                >
                    {{-- Header gradasi --}}
                    <div class="tw-relative tw-bg-gradient-to-br tw-px-5 tw-pb-4 tw-pt-5 tw-text-white
                        {{ $aktif ? 'tw-from-primary-600 tw-to-primary-900' : 'tw-from-primary-400 tw-to-primary-700 group-hover:tw-from-primary-500 group-hover:tw-to-primary-800' }}">
                        <div class="tw-pointer-events-none tw-absolute -tw-right-6 -tw-top-6 tw-h-24 tw-w-24 tw-rounded-full tw-bg-white/10"></div>

                        <div class="tw-relative tw-flex tw-items-start tw-justify-between tw-gap-3">
                            <h3 class="tw-text-lg tw-font-semibold tw-leading-snug">{{ $s['nama'] }}</h3>
                            @if ($aktif)
                                <span class="tw-shrink-0 tw-rounded-full tw-bg-white tw-px-2.5 tw-py-0.5 tw-text-xs tw-font-medium tw-text-primary-700">Dipilih</span>
                            @endif
                        </div>
                        <p class="tw-relative tw-mt-1 tw-text-sm tw-text-white/80">{{ $s['tempat'] }}</p>
                        <p class="tw-relative tw-text-sm tw-text-white/80">{{ $s['tanggal'] }}</p>
                    </div>

                    {{-- Isi kartu --}}
                    <div class="tw-flex tw-flex-1 tw-flex-col tw-gap-4 tw-p-5">
                        <div class="tw-grid tw-grid-cols-3 tw-gap-2.5">
                            <div class="tw-rounded-xl tw-bg-gray-100 tw-px-3 tw-py-2.5 dark:tw-bg-white/5">
                                <p class="tw-text-2xl tw-font-semibold tw-text-gray-950 dark:tw-text-white">{{ $s['peserta'] }}</p>
                                <p class="tw-text-xs tw-text-gray-500 dark:tw-text-gray-400">Peserta</p>
                            </div>
                            <div class="tw-rounded-xl tw-bg-success-50 tw-px-3 tw-py-2.5 dark:tw-bg-white/5">
                                <p class="tw-text-2xl tw-font-semibold tw-text-success-700 dark:tw-text-success-400">{{ $s['lulus'] }}</p>
                                <p class="tw-text-xs tw-text-success-700 tw-opacity-80 dark:tw-text-success-400">Lulus</p>
                            </div>
                            <div class="tw-rounded-xl tw-bg-danger-50 tw-px-3 tw-py-2.5 dark:tw-bg-white/5">
                                <p class="tw-text-2xl tw-font-semibold tw-text-danger-700 dark:tw-text-danger-400">{{ $s['ditolak'] }}</p>
                                <p class="tw-text-xs tw-leading-tight tw-text-danger-700 tw-opacity-80 dark:tw-text-danger-400">Belum memenuhi syarat</p>
                            </div>
                        </div>

                        {{-- Bar kelulusan: hijau = lulus, merah = belum memenuhi syarat --}}
                        <div>
                            <div class="tw-flex tw-h-2.5 tw-w-full tw-overflow-hidden tw-rounded-full tw-bg-gray-200 dark:tw-bg-white/10">
                                <div class="tw-h-full tw-bg-success-500" style="width: {{ $pctLulus }}%"></div>
                                <div class="tw-h-full tw-bg-danger-500" style="width: {{ $pctDitolak }}%"></div>
                            </div>
                            <div class="tw-mt-2 tw-flex tw-items-center tw-justify-between tw-text-xs tw-text-gray-500 dark:tw-text-gray-400">
                                <span>{{ $pctLulus }}% lulus</span>
                                <span>{{ $s['jumlah_materi'] }} materi aktif</span>
                            </div>
                        </div>

                        <div class="tw-mt-auto tw-flex tw-items-center tw-justify-between tw-border-t tw-border-gray-100 tw-pt-3 tw-text-sm tw-font-medium tw-text-primary-600 dark:tw-border-white/10 dark:tw-text-primary-400">
                            <span>{{ $aktif ? 'Sembunyikan peserta' : 'Lihat peserta' }}</span>
                            <svg class="tw-h-4 tw-w-4 tw-transition-transform {{ $aktif ? 'tw-rotate-90' : 'group-hover:tw-translate-x-1' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>

        {{-- ============ PANEL PESERTA (muncul setelah kartu diklik) ============ --}}
        @if ($terpilih)
            <section id="panel-peserta" class="tw-scroll-mt-20 tw-space-y-4">
                <div class="tw-flex tw-flex-wrap tw-items-center tw-justify-between tw-gap-3 tw-rounded-2xl tw-bg-gradient-to-r tw-from-primary-500 tw-to-primary-700 tw-px-5 tw-py-3.5 tw-text-white tw-shadow">
                    <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-x-5 tw-gap-y-1 tw-text-sm">
                        <span class="tw-font-semibold">{{ $terpilih['nama'] }}</span>
                        <span class="tw-text-white/80">{{ $terpilih['peserta'] }} peserta</span>
                        <span class="tw-text-white/80">{{ $terpilih['lulus'] }} lulus</span>
                        <span class="tw-text-white/80">{{ $terpilih['ditolak'] }} belum memenuhi syarat</span>
                    </div>

                    <button
                        type="button"
                        wire:click="pilihPelatihan(null)"
                        class="tw-rounded-lg tw-bg-white/15 tw-px-3 tw-py-1.5 tw-text-sm tw-font-medium tw-text-white tw-transition hover:tw-bg-white/25"
                    >
                        Tutup
                    </button>
                </div>

                {{ $this->table }}
            </section>
        @else
            <div class="tw-rounded-2xl tw-border-2 tw-border-dashed tw-border-gray-300 tw-px-6 tw-py-8 tw-text-center tw-text-sm tw-text-gray-500 dark:tw-border-white/15 dark:tw-text-gray-400">
                Pilih salah satu pelatihan di atas untuk melihat daftar pesertanya.
            </div>
        @endif
    @endif
</x-filament-panels::page>