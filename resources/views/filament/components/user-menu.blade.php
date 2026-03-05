@php
    use Illuminate\Support\Facades\Storage;
    use App\Filament\Pages\DashboardAnggota;

    $user    = auth()->user();
    $anggota = \App\Models\Anggota::find($user->anggota_id);

    $hasAnggota   = $anggota !== null;
    $dashboardUrl = $hasAnggota
        ? DashboardAnggota::getUrl(['anggotaId' => $anggota->id])
        : null;

    $fotoUrl     = $anggota?->foto ? Storage::url($anggota->foto) : null;
    $inisial     = strtoupper(substr($user->name, 0, 1));
    $namaLengkap = $user->nama_lengkap ?? $user->name;

    $role = match(true) {
        method_exists($user, 'getRoleNames') => $user->getRoleNames()->first() ?? 'Admin',
        isset($user->role)                   => $user->role,
        default                              => 'Admin',
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:click.outside="open = false"
    class="relative flex items-center"
>
    {{-- ─── Trigger Button ─────────────────────────────────────────── --}}
    <button
        type="button"
        x-on:click="open = !open"
        class="group flex items-center gap-3 rounded-xl px-2.5 py-1.5
               border border-transparent
               hover:border-gray-200 dark:hover:border-gray-700
               hover:bg-gray-50 dark:hover:bg-gray-800/60
               transition-all duration-200 focus:outline-none"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        {{-- Avatar --}}
        <div class="relative shrink-0">
            @if ($fotoUrl)
                <img
                    src="{{ $fotoUrl }}"
                    alt="{{ $namaLengkap }}"
                    class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-800"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                />
                <div
                    style="display:none"
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700
                           flex items-center justify-center ring-2 ring-white dark:ring-gray-800"
                >
                    <span class="text-white text-xs font-bold leading-none select-none">{{ $inisial }}</span>
                </div>
            @else
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700
                            flex items-center justify-center ring-2 ring-white dark:ring-gray-800">
                    <span class="text-white text-xs font-bold leading-none select-none">{{ $inisial }}</span>
                </div>
            @endif

            {{-- Online dot --}}
            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full
                         bg-emerald-400 ring-2 ring-white dark:ring-gray-900"></span>
        </div>

        {{-- Nama & Role --}}
        <div class="hidden sm:flex flex-col items-start leading-none min-w-0">
            <span class="text-[0.8125rem] font-semibold text-gray-800 dark:text-gray-100 leading-tight truncate max-w-[140px]">
                {{ $namaLengkap }}
            </span>
            <span class="text-[0.6875rem] text-gray-400 dark:text-gray-500 capitalize tracking-wide mt-0.5">
                {{ $role }}
            </span>
        </div>

        {{-- Chevron --}}
        <x-heroicon-m-chevron-down
            class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 transition-transform duration-200 shrink-0 hidden sm:block"
            ::class="{ 'rotate-180': open }"
        />
    </button>

    {{-- ─── Dropdown Panel ─────────────────────────────────────────── --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
        class="absolute right-0 top-full mt-2 w-60 z-50
               rounded-xl overflow-hidden
               border border-gray-200 dark:border-gray-700
               bg-white dark:bg-gray-900
               divide-y divide-gray-100 dark:divide-gray-800"
    >
        {{-- Header profil --}}
        <div class="px-4 py-3 flex items-center gap-3 bg-gray-50/70 dark:bg-gray-800/50">
            @if ($fotoUrl)
                <img src="{{ $fotoUrl }}" alt="{{ $namaLengkap }}"
                     class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-700 shrink-0" />
            @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-700
                            flex items-center justify-center ring-2 ring-white dark:ring-gray-700 shrink-0">
                    <span class="text-white text-sm font-bold select-none">{{ $inisial }}</span>
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $namaLengkap }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 capitalize mt-0.5 truncate">{{ $role }}</p>
            </div>
        </div>

        {{-- Menu Items --}}
        <div class="py-1">
            @if ($dashboardUrl)
                <a
                    href="{{ $dashboardUrl }}"
                    x-on:click="open = false"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors duration-150"
                >
                    <x-heroicon-o-user-circle class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
                    Dashboard Anggota
                </a>
            @endif

            <a
                href="{{ filament()->getProfileUrl() ?? '#' }}"
                x-on:click="open = false"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300
                       hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors duration-150"
            >
                <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
                Pengaturan Akun
            </a>
        </div>

        {{-- Sign Out --}}
        <div class="py-1">
            <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500
                           hover:bg-red-50 dark:hover:bg-red-500/10
                           transition-colors duration-150"
                >
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 shrink-0" />
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>