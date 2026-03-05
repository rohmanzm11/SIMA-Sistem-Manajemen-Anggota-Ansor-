<div>
    {{-- Header / Judul --}}
    <div style="text-align:center;margin-bottom:1.5rem;">
        <h1 style="font-size:1.5rem;font-weight:700;color:#111827;margin:0 0 0.25rem 0;">
            Buat Akun Baru
        </h1>
        <p style="font-size:0.875rem;color:#6b7280;margin:0;">
            Daftarkan diri Anda untuk mulai menggunakan layanan kami
        </p>
    </div>

    {{-- Alert error global --}}
    @if (session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.625rem;padding:0.75rem 1rem;font-size:0.83rem;color:#dc2626;margin-bottom:1rem;display:flex;gap:0.5rem;align-items:flex-start;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{ $this->form }}

    <div style="margin-top:1.25rem;">
        <x-filament::button
            wire:click="register"
            wire:loading.attr="disabled"
            size="lg"
            style="width:100%"
        >
            <span wire:loading.remove wire:target="register">Daftar Sekarang</span>
            <span wire:loading wire:target="register">Memproses...</span>
        </x-filament::button>
    </div>

    <div style="text-align:center;margin-top:1rem;font-size:0.85rem;color:#6b7280;">
        Sudah punya akun?
        <a href="{{ filament()->getLoginUrl() }}"
           style="color:#059669;font-weight:700;text-decoration:none;">
            Masuk di sini
        </a>
    </div>
</div>