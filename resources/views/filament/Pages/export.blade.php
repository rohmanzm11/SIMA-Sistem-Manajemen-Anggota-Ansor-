<x-filament-panels::page>

    <style>
        .export-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .export-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        .export-card {
            background: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
            border: 1px solid rgba(0, 0, 0, .06);
        }
        .dark .export-card {
            background: rgb(var(--gray-900));
            border-color: rgba(255, 255, 255, .1);
        }

        .export-header {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            color: #fff;
            background: linear-gradient(135deg, rgb(var(--primary-700)) 0%, rgb(var(--primary-500)) 55%, rgb(var(--primary-400)) 100%);
            overflow: hidden;
        }
        /* lingkaran dekoratif */
        .export-header::after {
            content: '';
            position: absolute;
            right: -2.5rem;
            top: -2.5rem;
            width: 9rem;
            height: 9rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, .12);
        }
        .export-header::before {
            content: '';
            position: absolute;
            right: 3rem;
            bottom: -3rem;
            width: 6rem;
            height: 6rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, .08);
        }

        .export-icon {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, .2);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .25);
        }
        .export-icon svg { width: 1.5rem; height: 1.5rem; }

        .export-title {
            position: relative;
            z-index: 1;
        }
        .export-title h2 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.5rem;
        }
        .export-title p {
            margin: 0.125rem 0 0;
            font-size: 0.875rem;
            opacity: .9;
        }

        .export-body { padding: 1.5rem; }
        .export-footer { margin-top: 1.5rem; }
    </style>

    <div class="export-grid">

        {{-- KIRI: SERTIFIKAT --}}
        <div class="export-card">
            <div class="export-header">
                <div class="export-icon">
                    <x-filament::icon icon="heroicon-o-document-text" />
                </div>
                <div class="export-title">
                    <h2>Export Sertifikat</h2>
                    <p>Semua peserta pelatihan diunduh dalam satu file ZIP.</p>
                </div>
            </div>

            <form wire:submit="exportSertifikat" class="export-body">
                {{ $this->sertifikatForm }}

                <div class="export-footer">
                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-arrow-down-tray"
                        wire:loading.attr="disabled"
                        wire:target="exportSertifikat"
                    >
                        Download Sertifikat
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- KANAN: KTA --}}
        <div class="export-card">
            <div class="export-header">
                <div class="export-icon">
                    <x-filament::icon icon="heroicon-o-identification" />
                </div>
                <div class="export-title">
                    <h2>Export KTA</h2>
                    <p>Unduh satu anggota (PDF) atau seluruh peserta satu pelatihan (ZIP).</p>
                </div>
            </div>

            <form wire:submit="exportKta" class="export-body">
                {{ $this->ktaForm }}

                <div class="export-footer">
                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-arrow-down-tray"
                        wire:loading.attr="disabled"
                        wire:target="exportKta"
                    >
                        Download KTA
                    </x-filament::button>
                </div>
            </form>
        </div>

    </div>
</x-filament-panels::page>