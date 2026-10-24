<x-filament-widgets::widget>
    <x-filament::section compact>
        <x-slot name="heading">Profil Pendidikan Anggota</x-slot>
        <x-slot name="description">Ringkasan jenjang pendidikan anggota</x-slot>

        {{-- Filter --}}
        <x-slot name="headerEnd">
            <div style="display:flex; gap:0.25rem; flex-wrap:wrap;">
                @foreach (['semua' => 'Semua', 'Lulus' => 'Lulus', 'Sedang Berjalan' => 'Berjalan', 'Tidak Lulus' => 'Tidak Lulus'] as $value => $label)
                    @php $aktif = $filterStatus === $value; @endphp
                    <x-filament::button
                        size="xs"
                        :color="$aktif ? 'primary' : 'gray'"
                        :outlined="! $aktif"
                        wire:click="setFilterStatus('{{ $value }}')">
                        {{ $label }}
                    </x-filament::button>
                @endforeach
            </div>
        </x-slot>

        @php
            $palette = [
                'blue'   => '#3b82f6',
                'purple' => '#8b5cf6',
                'indigo' => '#6366f1',
                'green'  => '#22c55e',
                'amber'  => '#f59e0b',
                'red'    => '#ef4444',
                'pink'   => '#ec4899',
                'teal'   => '#14b8a6',
            ];

            $ringkasan = [
                ['label' => 'Total',     'value' => $summary['total_riwayat'],         'color' => '#10b981'],
                ['label' => 'Lulus',     'value' => $summary['lulus_total'],           'color' => '#22c55e'],
                ['label' => 'Berjalan',  'value' => $summary['sedang_berjalan_total'], 'color' => '#f59e0b'],
                ['label' => 'Tdk Lulus', 'value' => $summary['tidak_lulus_total'],     'color' => '#ef4444'],
                ['label' => 'Sarjana+',  'value' => $summary['sarjana_plus'],          'color' => '#3b82f6'],
            ];
        @endphp

        {{-- Ringkasan: satu baris kecil --}}
        <div style="display:grid; grid-template-columns:repeat(5, minmax(0,1fr)); gap:0.5rem; margin-bottom:1rem;">
            @foreach ($ringkasan as $r)
                <div style="min-width:0; border-radius:0.75rem; padding:0.5rem 0.75rem; background:{{ $r['color'] }}1a; border:1px solid {{ $r['color'] }}40;">
                    <p style="font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:{{ $r['color'] }}; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $r['label'] }}</p>
                    <p style="font-size:1.25rem; font-weight:800; line-height:1.2; color:{{ $r['color'] }};">{{ number_format($r['value']) }}</p>
                </div>
            @endforeach
        </div>

        {{-- Kategori: daftar baris, 2 kolom di layar lebar --}}
        <div wire:loading.class="opacity-50"
             style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap:0.5rem;">
            @foreach ($categories as $cat)
                @php $c = $palette[$cat['color']] ?? '#22c55e'; @endphp

                <div style="min-width:0; display:flex; align-items:center; gap:0.75rem; border-radius:0.75rem; padding:0.5rem 0.75rem; background:{{ $c }}12; border:1px solid {{ $c }}33;">

                    {{-- Ikon --}}
                    <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:{{ $c }}; flex-shrink:0;">
                        @svg($cat['icon'], '', ['style' => 'width:1rem;height:1rem;color:#fff;'])
                    </div>

                    {{-- Nama + progress --}}
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:baseline; justify-content:space-between; gap:0.5rem;">
                            <p style="font-size:0.8125rem; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                               title="{{ $cat['subtitle'] }}">{{ $cat['nama'] }}</p>
                            <p style="font-size:0.75rem; font-weight:700; color:{{ $c }}; flex-shrink:0;">{{ $cat['percentage'] }}%</p>
                        </div>
                        <div style="height:0.25rem; border-radius:9999px; background:{{ $c }}26; margin-top:0.25rem; overflow:hidden;">
                            <div style="height:100%; width:{{ $cat['percentage'] }}%; background:{{ $c }}; border-radius:9999px; transition:width 0.5s ease;"></div>
                        </div>
                        <p style="font-size:0.625rem; opacity:0.6; margin-top:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $cat['subtitle'] }} · L {{ $cat['lulus'] }} · B {{ $cat['sedang_berjalan'] }} · TL {{ $cat['tidak_lulus'] }}
                        </p>
                    </div>

                    {{-- Jumlah --}}
                    <p style="font-size:1.25rem; font-weight:800; color:{{ $c }}; flex-shrink:0; min-width:1.75rem; text-align:right;">
                        {{ number_format($cat['count']) }}
                    </p>
                </div>
            @endforeach
        </div>

        @if ($summary['total_riwayat'] === 0)
            <p style="text-align:center; font-size:0.8125rem; opacity:0.6; margin-top:0.75rem;">Belum ada data pendidikan anggota.</p>
        @endif

        <p style="font-size:0.625rem; opacity:0.5; margin-top:0.75rem;">L = Lulus · B = Berjalan · TL = Tidak Lulus</p>
    </x-filament::section>
</x-filament-widgets::widget>