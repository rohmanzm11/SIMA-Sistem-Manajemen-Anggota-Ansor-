<x-filament-panels::page>

    {{-- HEADER INFO CARD --}}
    <div class="mb-6 rounded-xl border border-primary-200 bg-primary-50 p-4 dark:border-primary-800 dark:bg-primary-950">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex-shrink-0 text-primary-600 dark:text-primary-400">
                <x-heroicon-o-information-circle class="h-5 w-5" />
            </div>
            <div>
                <p class="text-sm font-semibold text-primary-800 dark:text-primary-200">
                    Formulir Pendaftaran Peserta
                </p>
                <p class="mt-0.5 text-xs text-primary-700 dark:text-primary-300">
                    Isi semua data yang diperlukan. Setelah disimpan, status anggota akan menjadi
                    <span class="font-semibold">Pending</span> hingga diverifikasi oleh admin.
                    NIA akan digenerate otomatis setelah data tersimpan.
                </p>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form wire:submit.prevent="submit" class="space-y-6">  {{-- ← tambah .prevent --}}

        <div>
            {{ $this->form }}
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-end gap-3 rounded-xl border border-gray-200 bg-white px-6 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">

            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-o-arrow-path"
                wire:click="$refresh"
                wire:confirm="Yakin ingin mereset semua isian form?"
                outlined
            >
                Reset Form
            </x-filament::button>

            <x-filament::button
                type="submit"
                color="primary"
                icon="heroicon-o-check-circle"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="submit">
                    Simpan Pendaftaran
                </span>
                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Menyimpan...
                </span>
            </x-filament::button>

        </div>

    </form>

    <x-filament-panels::unsaved-action-changes-alert />

</x-filament-panels::page>