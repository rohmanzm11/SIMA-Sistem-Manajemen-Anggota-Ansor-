<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-primary-500/10">
                    <x-heroicon-s-bolt class="w-4 h-4 text-primary-500" />
                </div>
                <span>Aktivitas Terbaru</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Riwayat pendaftaran dan verifikasi anggota
        </x-slot>

        @php $aktivitas = $this->getViewData()['aktivitas']; @endphp

        @if ($aktivitas->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                    <x-heroicon-o-inbox class="w-7 h-7 text-gray-400 dark:text-gray-500" />
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada aktivitas</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Aktivitas akan muncul di sini</p>
            </div>
        @else
            <div class="space-y-0">
                @foreach ($aktivitas as $item)
                    @php
                        $colorMap = [
                            'blue'  => [
                                'dot'        => '#3b82f6',
                                'line'       => '#bfdbfe',
                                'badgeBg'    => '#eff6ff',
                                'badgeText'  => '#1d4ed8',
                                'badgeBorder'=> '#bfdbfe',
                            ],
                            'green' => [
                                'dot'        => '#10b981',
                                'line'       => '#a7f3d0',
                                'badgeBg'    => '#ecfdf5',
                                'badgeText'  => '#065f46',
                                'badgeBorder'=> '#6ee7b7',
                            ],
                            'red'   => [
                                'dot'        => '#f43f5e',
                                'line'       => '#fecdd3',
                                'badgeBg'    => '#fff1f2',
                                'badgeText'  => '#be123c',
                                'badgeBorder'=> '#fda4af',
                            ],
                        ];
                        $c = $colorMap[$item['color']] ?? [
                            'dot'        => '#9ca3af',
                            'line'       => '#e5e7eb',
                            'badgeBg'    => '#f9fafb',
                            'badgeText'  => '#374151',
                            'badgeBorder'=> '#d1d5db',
                        ];
                        $isLast = $loop->last;
                    @endphp

                    <div class="group relative flex gap-3 rounded-xl px-3 py-2.5 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-white/[0.03]">

                        {{-- Timeline dot + connector --}}
                        <div class="relative flex flex-col items-center pt-1 flex-shrink-0">
                            {{-- Dot --}}
                            <div
                                class="relative z-10 flex items-center justify-center w-7 h-7 rounded-full shadow-md transition-transform duration-200 group-hover:scale-110"
                                style="background-color: {{ $c['dot'] }}; box-shadow: 0 4px 6px -1px {{ $c['dot'] }}4d;"
                            >
                                @svg($item['icon'], 'w-3.5 h-3.5 text-white')
                            </div>

                            {{-- Connector line --}}
                            @unless ($isLast)
                                <div
                                    class="w-px flex-1 mt-1"
                                    style="background-color: {{ $c['line'] }}; min-height: 1.5rem;"
                                ></div>
                            @endunless
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0 {{ $isLast ? 'pb-0' : 'pb-2' }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center flex-wrap gap-1.5 min-w-0">
                                    <a href="{{ $item['url'] }}"
                                       class="text-sm font-semibold text-gray-800 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 truncate transition-colors">
                                        {{ $item['nama'] }}
                                    </a>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 text-[0.68rem] font-semibold rounded-full whitespace-nowrap"
                                        style="background-color: {{ $c['badgeBg'] }}; color: {{ $c['badgeText'] }}; border: 1px solid {{ $c['badgeBorder'] }};"
                                    >
                                        {{ $item['label'] }}
                                    </span>
                                </div>
                                <time class="flex-shrink-0 text-[0.7rem] font-medium text-gray-400 dark:text-gray-500 pt-0.5 whitespace-nowrap">
                                    {{ $item['waktu']->diffForHumans() }}
                                </time>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5">
                <a href="{{ route('filament.admin.resources.anggotas.index') }}"
                   class="group flex items-center justify-center gap-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    <span>Lihat semua anggota</span>
                    <x-heroicon-m-arrow-right class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-0.5" />
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>