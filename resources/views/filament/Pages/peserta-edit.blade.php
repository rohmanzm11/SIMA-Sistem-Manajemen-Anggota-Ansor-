<x-filament-panels::page>

    {{-- HEADER INFO CARD --}}
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex-shrink-0 text-amber-600 dark:text-amber-400">
                <x-heroicon-o-pencil-square class="h-5 w-5" />
            </div>
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                    Edit Data Peserta
                </p>
                <p class="mt-0.5 text-xs text-amber-700 dark:text-amber-300">
                    Perbarui informasi peserta di bawah ini. Perubahan akan disimpan setelah Anda menekan tombol
                    <span class="font-semibold">Simpan Perubahan</span>.
                    NIK tidak dapat diubah karena sudah ter-verifikasi.
                </p>
            </div>
        </div>
    </div>

    {{-- ANGGOTA INFO CARD --}}
    @if ($anggota)
        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                        {{ $anggota->nama_lengkap }}
                    </p>
                    <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                        <span class="font-semibold">NIA:</span> {{ $anggota->nia ?? '—' }} | 
                        <span class="font-semibold">NIK:</span> {{ $anggota->nik ?? '—' }} |
                        <span class="font-semibold">Status:</span> 
                        <span class="ml-1 inline-block rounded-full px-2 py-1 text-xs font-medium 
                            @if ($anggota->status_verifikasi === 'Approved')
                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif ($anggota->status_verifikasi === 'Rejected')
                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @else
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @endif
                        ">
                            {{ $anggota->status_verifikasi ?? 'Pending' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- FORM --}}
    <form wire:submit.prevent="submit" class="space-y-6">

        {{-- RENDER FORM SECTIONS --}}
        <div>
            {{ $this->form }}
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-end gap-3 rounded-xl border border-gray-200 bg-white px-6 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">

            {{-- RESET BUTTON --}}
            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-o-arrow-path"
                wire:click="resetForm"
                wire:confirm="Yakin ingin mereset semua perubahan?"
                outlined
            >
                Reset Form
            </x-filament::button>

            {{-- SUBMIT BUTTON --}}
            <x-filament::button
                type="submit"
                color="primary"
                icon="heroicon-o-check-circle"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="submit">
                    Simpan Perubahan
                </span>
                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Menyimpan...
                </span>
            </x-filament::button>

        </div>

    </form>

    {{-- UNSAVED CHANGES ALERT --}}
    <x-filament-panels::unsaved-action-changes-alert />

</x-filament-panels::page>