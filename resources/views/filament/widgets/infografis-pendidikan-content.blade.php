<div class="space-y-6">
    @php
        $data = $this->getData();
        $categories = $data['categories'];
        $summary = $data['summary'];
        
        // Map warna untuk dynamic styling
        $colorClasses = [
            'blue' => 'border-blue-500 text-blue-600 dark:text-blue-400 bg-gradient-to-r from-blue-400 to-blue-600',
            'purple' => 'border-purple-500 text-purple-600 dark:text-purple-400 bg-gradient-to-r from-purple-400 to-purple-600',
            'green' => 'border-green-500 text-green-600 dark:text-green-400 bg-gradient-to-r from-green-400 to-green-600',
            'amber' => 'border-amber-500 text-amber-600 dark:text-amber-400 bg-gradient-to-r from-amber-400 to-amber-600',
            'red' => 'border-red-500 text-red-600 dark:text-red-400 bg-gradient-to-r from-red-400 to-red-600',
            'pink' => 'border-pink-500 text-pink-600 dark:text-pink-400 bg-gradient-to-r from-pink-400 to-pink-600',
            'teal' => 'border-teal-500 text-teal-600 dark:text-teal-400 bg-gradient-to-r from-teal-400 to-teal-600',
        ];
    @endphp

    <!-- Header -->
    <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            Profil Pendidikan Anggota
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Ringkasan Tingkat Pendidikan Formal - 7 Kategori
        </p>
    </div>

    <!-- Filter Status -->
    <div class="flex gap-3 flex-wrap mb-6">
        @foreach(['semua' => 'Semua', 'Lulus' => 'Lulus', 'Sedang Belajar' => 'Sedang Belajar', 'Berhenti' => 'Berhenti'] as $value => $label)
            <button
                wire:click="setFilterStatus('{{ $value }}')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                @if($data['filterStatus'] === $value) 
                    bg-indigo-600 text-white shadow-md 
                @else 
                    bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 
                @endif"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Grid Kategori - Clean & Minimalist -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4">
        @foreach($categories as $cat)
            @php
                $borderColor = match($cat['color']) {
                    'blue' => 'border-l-4 border-blue-500',
                    'purple' => 'border-l-4 border-purple-500',
                    'green' => 'border-l-4 border-green-500',
                    'amber' => 'border-l-4 border-amber-500',
                    'red' => 'border-l-4 border-red-500',
                    'pink' => 'border-l-4 border-pink-500',
                    'teal' => 'border-l-4 border-teal-500',
                    default => 'border-l-4 border-gray-500',
                };
                
                $textColor = match($cat['color']) {
                    'blue' => 'text-blue-600 dark:text-blue-400',
                    'purple' => 'text-purple-600 dark:text-purple-400',
                    'green' => 'text-green-600 dark:text-green-400',
                    'amber' => 'text-amber-600 dark:text-amber-400',
                    'red' => 'text-red-600 dark:text-red-400',
                    'pink' => 'text-pink-600 dark:text-pink-400',
                    'teal' => 'text-teal-600 dark:text-teal-400',
                    default => 'text-gray-600 dark:text-gray-400',
                };
                
                $gradientClass = match($cat['color']) {
                    'blue' => 'from-blue-400 to-blue-600',
                    'purple' => 'from-purple-400 to-purple-600',
                    'green' => 'from-green-400 to-green-600',
                    'amber' => 'from-amber-400 to-amber-600',
                    'red' => 'from-red-400 to-red-600',
                    'pink' => 'from-pink-400 to-pink-600',
                    'teal' => 'from-teal-400 to-teal-600',
                    default => 'from-gray-400 to-gray-600',
                };
                
                $labelColor = match($cat['color']) {
                    'blue' => 'text-blue-600 dark:text-blue-400 font-semibold',
                    'purple' => 'text-purple-600 dark:text-purple-400 font-semibold',
                    'green' => 'text-green-600 dark:text-green-400 font-semibold',
                    'amber' => 'text-amber-600 dark:text-amber-400 font-semibold',
                    'red' => 'text-red-600 dark:text-red-400 font-semibold',
                    'pink' => 'text-pink-600 dark:text-pink-400 font-semibold',
                    'teal' => 'text-teal-600 dark:text-teal-400 font-semibold',
                    default => 'text-gray-600 dark:text-gray-400 font-semibold',
                };
            @endphp
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow duration-300 {{ $borderColor }}">
                
                <!-- Top Gradient Line -->
                <div class="h-1 bg-gradient-to-r {{ $gradientClass }}"></div>

                <!-- Content -->
                <div class="p-5">
                    <!-- Title -->
                    <div class="mb-4">
                        <p class="text-sm {{ $labelColor }} truncate">
                            {{ $cat['nama'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $cat['subtitle'] }}
                        </p>
                    </div>

                    <!-- Main Count -->
                    <div class="mb-5">
                        <p class="text-4xl font-bold {{ $textColor }} leading-tight">
                            {{ $cat['count'] }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1.5 font-medium">
                            {{ $cat['percentage'] }}% dari total
                        </p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-5">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="h-full bg-gradient-to-r {{ $gradientClass }} rounded-full transition-all duration-700 ease-out"
                                 style="width: {{ min($cat['percentage'] * 2.5, 100) }}%; opacity: 1;">
                            </div>
                        </div>
                    </div>

                    <!-- Status Breakdown -->
                    <div class="space-y-2 text-xs border-t border-gray-200 dark:border-gray-700 pt-4">
                        @if($cat['lulus'] > 0 || $cat['sedang_belajar'] > 0 || $cat['berhenti'] > 0)
                            @if($cat['lulus'] > 0)
                                <div class="flex items-center justify-between text-gray-700 dark:text-gray-300">
                                    <span class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0"></span>
                                        <span>Lulus</span>
                                    </span>
                                    <span class="font-semibold {{ $textColor }} flex-shrink-0">{{ $cat['lulus'] }}</span>
                                </div>
                            @endif
                            @if($cat['sedang_belajar'] > 0)
                                <div class="flex items-center justify-between text-gray-700 dark:text-gray-300">
                                    <span class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full flex-shrink-0"></span>
                                        <span>Belajar</span>
                                    </span>
                                    <span class="font-semibold {{ $textColor }} flex-shrink-0">{{ $cat['sedang_belajar'] }}</span>
                                </div>
                            @endif
                            @if($cat['berhenti'] > 0)
                                <div class="flex items-center justify-between text-gray-700 dark:text-gray-300">
                                    <span class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></span>
                                        <span>Berhenti</span>
                                    </span>
                                    <span class="font-semibold {{ $textColor }} flex-shrink-0">{{ $cat['berhenti'] }}</span>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-1.5">-</p>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700/30 px-5 py-3 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-700 dark:text-gray-300 font-medium truncate">
                        {{ implode(', ', $cat['jenjang']) }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Summary Statistics -->
    <div class="mt-8">
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-gray-800 dark:to-gray-800 rounded-lg shadow-sm p-8 border border-gray-200 dark:border-gray-700">
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Ringkasan Statistik
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total data pendidikan anggota</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Total -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ $summary['total_riwayat'] }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-3 font-semibold">
                            Total
                        </p>
                    </div>
                </div>

                <!-- Lulus -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                            {{ $summary['persen_lulus'] }}%
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-3 font-semibold">
                            Lulus
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                            ({{ $summary['lulus_total'] }})
                        </p>
                    </div>
                </div>

                <!-- Sedang Belajar -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $summary['sedang_belajar_total'] }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-3 font-semibold">
                            Sedang Belajar
                        </p>
                    </div>
                </div>

                <!-- S1+ -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $summary['persen_sarjana'] }}%
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-3 font-semibold">
                            S1+
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                            ({{ $summary['sarjana_plus'] }})
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>