<x-filament-panels::page>

    {{-- ══════════════════════════════════════════════════════
         HERO HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden"
         style="background: linear-gradient(135deg, #064e3b 0%, #059669 55%, #34d399 100%); padding: 2rem 2rem 2rem; margin-bottom: 1.5rem; border-radius: 1.5rem; box-shadow: 0 4px 24px rgba(5,150,105,0.18);">

        {{-- Dekorasi lingkaran --}}
        <div style="pointer-events:none;position:absolute;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,0.06);top:-100px;right:-80px;"></div>
        <div style="pointer-events:none;position:absolute;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-60px;left:-40px;"></div>
        <div style="pointer-events:none;position:absolute;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.05);bottom:20px;right:320px;"></div>

        {{-- Top row: title left, search + stats right --}}
        <div style="display:flex; gap:2rem; align-items:flex-start;">

            {{-- Left: icon + title --}}
            <div style="display:flex; align-items:center; gap:0.875rem; flex-shrink:0;">
                <div style="display:flex; align-items:center; justify-content:center; width:3.25rem; height:3.25rem; border-radius:1rem; background:rgba(255,255,255,0.15); flex-shrink:0;">
                    <x-heroicon-s-users style="width:1.75rem; height:1.75rem; color:white;" />
                </div>
                <div>
                    <h1 style="font-size:1.5rem; font-weight:900; color:#fff; line-height:1.2; letter-spacing:-0.02em;">Manajemen Anggota</h1>
                    <p style="font-size:0.75rem; font-weight:500; color:#a7f3d0; margin-top:0.125rem;">Kelola seluruh data keanggotaan organisasi</p>
                </div>
            </div>

            {{-- Right: search + mini stats --}}
            <div style="flex:1; display:flex; flex-direction:column; gap:0.75rem; align-items:flex-end;">

                {{-- Search bar inside hero --}}
                <div style="position:relative; width:280px;">
                    <div style="position:absolute; top:50%; left:0.75rem; transform:translateY(-50%); pointer-events:none;">
                        <x-heroicon-o-magnifying-glass style="width:1rem; height:1rem; color:rgba(255,255,255,0.6);" />
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Cari nama, NIA, atau nomor HP..."
                        style="width:100%; border-radius:0.75rem; padding:0.5rem 1rem 0.5rem 2.25rem; font-size:0.8125rem; font-weight:500; color:#fff; background:rgba(255,255,255,0.12); border:1.5px solid rgba(255,255,255,0.2); outline:none; backdrop-filter:blur(8px); box-sizing:border-box;"
                        placeholder-color="rgba(255,255,255,0.5)"
                        onfocus="this.style.borderColor='rgba(255,255,255,0.5)';this.style.background='rgba(255,255,255,0.18)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.2)';this.style.background='rgba(255,255,255,0.12)'"
                    />
                </div>

                {{-- Stats row --}}
                @php $stat = $this->statistik; @endphp
                <div style="display:flex; gap:0.625rem;">
                    @foreach ([
                        ['label' => 'Total',       'value' => $stat['total'],        'icon' => 'heroicon-s-users',       'color' => 'rgba(52,211,153,0.25)',  'border' => 'rgba(52,211,153,0.35)'],
                        ['label' => 'Diverifikasi','value' => $stat['diverifikasi'], 'icon' => 'heroicon-s-check-badge', 'color' => 'rgba(52,211,153,0.25)',  'border' => 'rgba(52,211,153,0.35)'],
                        ['label' => 'Pending',     'value' => $stat['pending'],      'icon' => 'heroicon-s-clock',       'color' => 'rgba(251,191,36,0.2)',   'border' => 'rgba(251,191,36,0.4)'],
                        ['label' => 'Ditolak',     'value' => $stat['ditolak'],      'icon' => 'heroicon-s-x-circle',    'color' => 'rgba(248,113,113,0.2)',  'border' => 'rgba(248,113,113,0.4)'],
                    ] as $s)
                        <div style="display:flex; align-items:center; gap:0.625rem; border-radius:0.875rem; padding:0.625rem 0.875rem; background:{{ $s['color'] }}; border:1.5px solid {{ $s['border'] }}; backdrop-filter:blur(8px);">
                            <div style="display:flex; align-items:center; justify-content:center; width:1.875rem; height:1.875rem; border-radius:0.5rem; background:rgba(255,255,255,0.1); flex-shrink:0;">
                                @svg($s['icon'], 'w-3.5 h-3.5', ['style' => 'color:white; width:0.875rem; height:0.875rem;'])
                            </div>
                            <div>
                                <p style="font-size:1.25rem; font-weight:900; color:#fff; line-height:1;">{{ $s['value'] }}</p>
                                <p style="font-size:0.5625rem; font-weight:600; color:#a7f3d0; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap;">{{ $s['label'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════
         FILTER BAR
    ══════════════════════════════════════════════════════ --}}
    <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; background:#fff; border:1.5px solid #a7f3d0; border-radius:1rem; padding:0.875rem 1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 8px rgba(5,150,105,0.06);">

        {{-- Filter Status --}}
        <div style="position:relative;">
            <select wire:model.live="filterStatus"
                    style="appearance:none; border-radius:0.75rem; padding:0.5rem 2rem 0.5rem 0.875rem; font-size:0.8125rem; font-weight:600; color:#374151; background:#f0fdf4; border:1.5px solid #6ee7b7; outline:none; cursor:pointer;">
                <option value="">Semua Status</option>
                <option value="Diverifikasi">✅ Diverifikasi</option>
                <option value="Pending">🕐 Pending</option>
                <option value="Ditolak">❌ Ditolak</option>
            </select>
            <div style="pointer-events:none; position:absolute; top:50%; right:0.625rem; transform:translateY(-50%);">
                <x-heroicon-o-chevron-down style="width:0.875rem; height:0.875rem; color:#059669;" />
            </div>
        </div>

        {{-- Filter Kecamatan --}}
        <div style="position:relative;">
            <select wire:model.live="filterKec"
                    style="appearance:none; border-radius:0.75rem; padding:0.5rem 2rem 0.5rem 0.875rem; font-size:0.8125rem; font-weight:600; color:#374151; background:#f0fdf4; border:1.5px solid #6ee7b7; outline:none; cursor:pointer;">
                <option value="">Semua Kecamatan</option>
                @foreach ($this->kecamatanList as $kec)
                    <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
                @endforeach
            </select>
            <div style="pointer-events:none; position:absolute; top:50%; right:0.625rem; transform:translateY(-50%);">
                <x-heroicon-o-chevron-down style="width:0.875rem; height:0.875rem; color:#059669;" />
            </div>
        </div>

        {{-- Reset --}}
        @if ($search || $filterStatus || $filterKec)
            <button wire:click="resetFilter"
                    style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.75rem; padding:0.5rem 0.875rem; font-size:0.75rem; font-weight:700; background:#fee2e2; color:#e11d48; border:1.5px solid #fca5a5; cursor:pointer; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                <x-heroicon-o-x-mark style="width:0.875rem; height:0.875rem;" />
                Reset
            </button>
        @endif

        {{-- Spacer --}}
        <div style="margin-left:auto; display:flex; align-items:center; gap:0.5rem;">
            <div style="display:flex; align-items:center; gap:0.375rem; border-radius:0.75rem; padding:0.375rem 0.875rem; background:#f0fdf4; border:1.5px solid #6ee7b7;">
                <x-heroicon-o-users style="width:0.875rem; height:0.875rem; color:#059669;" />
                <span style="font-size:0.8125rem; font-weight:700; color:#059669;">
                    {{ $this->anggota->count() }} Anggota Ditemukan
                </span>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════
         EMPTY STATE
    ══════════════════════════════════════════════════════ --}}
    @if ($this->anggota->isEmpty())
        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.75rem; border-radius:1.25rem; background:#fff; padding:6rem 2rem; border:1.5px solid #a7f3d0;">
            <div style="display:flex; align-items:center; justify-content:center; width:4rem; height:4rem; border-radius:1rem; background:#d1fae5;">
                <x-heroicon-o-users style="width:2rem; height:2rem; color:#059669;" />
            </div>
            <p style="font-size:1rem; font-weight:700; color:#6b7280;">Tidak ada anggota ditemukan</p>
            <p style="font-size:0.875rem; color:#9ca3af;">Coba ubah filter atau kata kunci pencarian</p>
        </div>

    {{-- ══════════════════════════════════════════════════════
         CARD GRID
    ══════════════════════════════════════════════════════ --}}
  @else
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1.25rem;">
            @foreach ($this->anggota as $anggota)
                @php
                    [$vBg, $vText, $vBorder, $vDot] = match($anggota->status_verifikasi) {
                        'Diverifikasi' => ['#d1fae5', '#065f46', '#6ee7b7', '#10b981'],
                        'Pending'      => ['#fef9c3', '#854d0e', '#fcd34d', '#f59e0b'],
                        default        => ['#fee2e2', '#9f1239', '#fca5a5', '#f43f5e'],
                    };

                    $jabatanAktif = $anggota->strukturOrganisasi
                        ->where('is_active', true)
                        ->sortByDesc('masa_khidmat_mulai')
                        ->first();
                @endphp

                <a href="{{ \App\Filament\Pages\DashboardAnggota::getUrl(['anggotaId' => $anggota->id]) }}"
                   style="display:flex; flex-direction:column; overflow:hidden; border-radius:1.25rem; background:#fff; border:1.5px solid #a7f3d0; box-shadow:0 4px 16px rgba(5,150,105,0.1); text-decoration:none; transition:all 0.25s ease;"
                   onmouseover="this.style.boxShadow='0 12px 36px rgba(5,150,105,0.2)';this.style.borderColor='#34d399';this.style.transform='translateY(-4px)'"
                   onmouseout="this.style.boxShadow='0 4px 16px rgba(5,150,105,0.1)';this.style.borderColor='#a7f3d0';this.style.transform='translateY(0)'">

                    {{-- ===== CARD HEADER dengan gradient hijau ===== --}}
                    <div class="relative overflow-hidden"
                         style="background: linear-gradient(135deg, #064e3b 0%, #059669 55%, #34d399 100%); padding: 1.25rem; position:relative;">

                        {{-- Dekorasi lingkaran --}}
                        <div style="pointer-events:none;position:absolute;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);top:-40px;right:-30px;"></div>
                        <div style="pointer-events:none;position:absolute;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-30px;left:-20px;"></div>
                        <div style="pointer-events:none;position:absolute;width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,0.05);bottom:10px;right:60px;"></div>

                        {{-- Status badge --}}
                        <div style="position:relative; display:flex; justify-content:flex-end; margin-bottom:0.625rem;">
                            <span style="display:inline-flex; align-items:center; gap:0.25rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.6875rem; font-weight:700; background:{{ $vBg }}; color:{{ $vText }}; border:1.5px solid {{ $vBorder }}; white-space:nowrap;">
                                {{ $anggota->status_verifikasi }}
                            </span>
                        </div>

                        {{-- Avatar + Name --}}
                        <div style="position:relative; display:flex; align-items:center; gap:0.875rem;">
                            <div style="flex-shrink:0; position:relative;">
                                @if ($anggota->foto)
                                    <img src="{{ asset('storage/' . $anggota->foto) }}"
                                         alt="{{ $anggota->nama_lengkap }}"
                                         style="width:4rem; height:6rem; border-radius:0.875rem; object-fit:cover; border:2.5px solid rgba(255,255,255,0.5); box-shadow:0 4px 14px rgba(0,0,0,0.25);" />
                                @else
                                    <div style="width:4rem; height:4rem; border-radius:0.875rem; background:rgba(255,255,255,0.15); border:2.5px solid rgba(255,255,255,0.4); box-shadow:0 4px 14px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center;">
                                        <span style="font-size:1.5rem; font-weight:900; color:#fff;">
                                            {{ strtoupper(substr($anggota->nama_lengkap, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                {{-- Status dot --}}
                                <span style="position:absolute; bottom:-3px; right:-3px; width:0.9375rem; height:0.9375rem; border-radius:50%; background:{{ $vDot }}; box-shadow:0 0 0 2.5px #fff;"></span>
                            </div>

                            <div style="flex:1; min-width:0;">
                                <p style="font-size:0.9375rem; font-weight:800; color:#fff; line-height:1.3; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                    {{ $anggota->nama_lengkap }}
                                </p>
                                @if ($anggota->nia)
                                    <p style="font-size:0.6875rem; font-weight:600; color:#a7f3d0; font-family:monospace; margin-top:0.2rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $anggota->nia }}
                                    </p>
                                @else
                                    <p style="font-size:0.6875rem; color:rgba(255,255,255,0.4); font-style:italic; margin-top:0.2rem;">NIA belum di-generate</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ===== CARD BODY ===== --}}
                    <div style="padding:1rem 1.25rem 0; display:flex; flex-direction:column; gap:0.5rem; flex:1;">

                        {{-- Divider --}}
                        <div style="border-top:1px dashed #a7f3d0;"></div>

                        {{-- Info rows --}}
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            @if ($anggota->kecamatan)
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <x-heroicon-o-map-pin style="width:0.9375rem; height:0.9375rem; color:#34d399; flex-shrink:0;" />
                                    <span style="font-size:0.8125rem; color:#6b7280; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $anggota->kecamatan->nama_kecamatan }}{{ $anggota->desa ? ' — ' . $anggota->desa->nama_desa : '' }}</span>
                                </div>
                            @endif

                            @if ($anggota->nomor_hp)
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <x-heroicon-o-phone style="width:0.9375rem; height:0.9375rem; color:#34d399; flex-shrink:0;" />
                                    <span style="font-size:0.8125rem; color:#6b7280; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $anggota->nomor_hp }}</span>
                                </div>
                            @endif

                            @if ($jabatanAktif)
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <x-heroicon-o-briefcase style="width:0.9375rem; height:0.9375rem; color:#34d399; flex-shrink:0;" />
                                    <span style="font-size:0.8125rem; color:#6b7280; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $jabatanAktif->jabatan?->nama_jabatan ?? '—' }}</span>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- ===== FOOTER CTA ===== --}}
                    <div style="padding:1rem 1.25rem 1.25rem;">
                        <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem; border-radius:0.875rem; padding:0.625rem; background:#ecfdf5; border:1.5px solid #6ee7b7; transition:background 0.15s;"
                             onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">
                            <x-heroicon-o-arrow-right style="width:0.9375rem; height:0.9375rem; color:#065f46;" />
                            <span style="font-size:0.8125rem; font-weight:700; color:#065f46;">Lihat Detail</span>
                        </div>
                    </div>

                </a>
            @endforeach
        </div>

        {{-- Bottom info --}}
        <div style="margin-top:1rem; text-align:center; font-size:0.8125rem; color:#9ca3af;">
            Menampilkan <span style="font-weight:700; color:#059669;">{{ $this->anggota->count() }}</span> dari
            <span style="font-weight:700; color:#374151;">{{ $this->statistik['total'] }}</span> total anggota
        </div>
    @endif
</x-filament-panels::page>