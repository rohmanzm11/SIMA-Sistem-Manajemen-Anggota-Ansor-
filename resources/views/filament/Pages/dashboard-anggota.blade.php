<x-filament-panels::page>

    {{-- ══════════════════════════════════════════════════════
         HERO HEADER — EDIT DATA PESERTA (EMERALD THEME)
    ══════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden"
         style="background: linear-gradient(135deg, #064e3b 0%, #059669 55%, #34d399 100%); padding: 2rem; margin-bottom: 1.5rem; border-radius: 1.5rem; box-shadow: 0 4px 24px rgba(5,150,105,0.18);">

        {{-- Background Decorative Circles --}}
        <div style="pointer-events:none;position:absolute;width:320px;height:320px;border-radius:50%;background:rgba(255,255,255,0.05);top:-120px;right:-80px;"></div>
        <div style="pointer-events:none;position:absolute;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:-50px;left:-30px;"></div>
        <div style="pointer-events:none;position:absolute;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.04);bottom:30px;right:280px;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:1.75rem; flex-wrap:wrap;">

            {{-- Avatar --}}
            <div style="flex-shrink:0;">
                @if ($anggota->foto)
                    <img src="{{ asset('storage/' . $anggota->foto) }}"
                         alt="{{ $anggota->nama_lengkap }}"
                         style="width:5.5rem; height:5.5rem; border-radius:1rem; object-fit:cover; border:3px solid rgba(255,255,255,0.45); box-shadow:0 8px 32px rgba(0,0,0,0.25);" />
                @else
                    <div style="width:5.5rem; height:5.5rem; border-radius:1rem; background:rgba(52,211,153,0.25); border:3px solid rgba(255,255,255,0.35); box-shadow:0 8px 32px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center;">
                        <span style="font-size:2.25rem; font-weight:900; color:#fff;">
                            {{ strtoupper(substr($anggota->nama_lengkap, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div style="flex:1; min-width:0;">
                {{-- Label --}}
                <p style="font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#a7f3d0; margin-bottom:0.25rem;">
                    ✏️ Sunting Data Anggota
                </p>
                
                {{-- Nama --}}
                <h1 style="font-size:1.5rem; font-weight:900; color:#fff; letter-spacing:-0.02em; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $anggota->nama_lengkap }}
                </h1>

                {{-- Badge Info --}}
                <div style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:0.75rem;">
                    @if ($anggota->nia)
                        <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:700; color:#fff; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);">
                            <x-heroicon-o-identification style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                            NIA: {{ $anggota->nia }}
                        </span>
                    @endif
                    
                    @if ($anggota->nik)
                        <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:700; color:#fff; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);">
                            <x-heroicon-o-document-text style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                            NIK: {{ $anggota->nik }}
                        </span>
                    @endif

                    @if ($anggota->nomor_hp)
                        <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:700; color:#fff; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);">
                            <x-heroicon-o-phone style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                            {{ $anggota->nomor_hp }}
                        </span>
                    @endif

                    @if ($anggota->kecamatan)
                        <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:700; color:#fff; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);">
                            <x-heroicon-o-map-pin style="width:0.875rem; height:0.875rem; flex-shrink:0;" />
                            {{ $anggota->kecamatan->nama_kecamatan }}{{ $anggota->desa ? ' — ' . $anggota->desa->nama_desa : '' }}
                        </span>
                    @endif

                    {{-- Status Badge --}}
                    @php
                        [$vBg, $vBorder, $vIcon] = match($anggota->status_verifikasi ?? '') {
                            'Diverifikasi' => ['rgba(34,197,94,0.25)',  'rgba(34,197,94,0.5)',  'heroicon-s-check-badge'],
                            'Pending'      => ['rgba(251,191,36,0.25)',  'rgba(251,191,36,0.5)',  'heroicon-s-clock'],
                            'Ditolak'      => ['rgba(239,68,68,0.25)',   'rgba(239,68,68,0.5)',   'heroicon-s-x-circle'],
                            default        => ['rgba(107,114,128,0.25)', 'rgba(107,114,128,0.5)', 'heroicon-s-question-mark-circle'],
                        };
                    @endphp
                    <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:9999px; padding:0.25rem 0.75rem; font-size:0.75rem; font-weight:700; color:#fff; background:{{ $vBg }}; border:1px solid {{ $vBorder }};">
                        @svg($vIcon, 'w-3.5 h-3.5 shrink-0', ['style' => 'width:0.875rem;height:0.875rem;flex-shrink:0;'])
                        {{ $anggota->status_verifikasi ?? 'Tidak Diketahui' }}
                    </span>
                </div>
            </div>

            {{-- Tombol Kembali --}}
            <a href="{{ url()->previous() }}"
               style="flex-shrink:0; align-self:flex-start; display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.875rem; padding:0.5rem 1rem; font-size:0.75rem; font-weight:700; color:#fff; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); backdrop-filter:blur(8px); text-decoration:none; transition:opacity 0.15s;"
               onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                <x-heroicon-o-arrow-left style="width:0.875rem; height:0.875rem;" />
                Kembali
            </a>
          <a href="{{ \App\Filament\Pages\PesertaEdit::getUrl(['anggotaId' => $this->anggota->id]) }}"
            style="flex-shrink:0; align-self:flex-start; display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.875rem; padding:0.5rem 1rem; font-size:0.75rem; font-weight:700; color:#fff; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); backdrop-filter:blur(8px); text-decoration:none; transition:opacity 0.15s;"
   wire:navigate>
    Edit Profil
</a>

        </div>
    </div>

   


    {{-- ══════════════════════════════════════════════════════
         STATISTIK KEHADIRAN
    ══════════════════════════════════════════════════════ --}}
    @php $stat = $this->getStatistik(); @endphp

    <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:0.75rem; margin-bottom:1.25rem;">
        @foreach ([
            ['label' => 'Total Sesi',  'value' => $stat['total_sesi'],                                                    'icon' => 'heroicon-o-academic-cap',   'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
            ['label' => 'Hadir',       'value' => $stat['hadir'],                                                          'icon' => 'heroicon-o-check-circle',   'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
            ['label' => 'Tidak Hadir', 'value' => $stat['tidak_hadir'],                                                    'icon' => 'heroicon-o-x-circle',       'color' => '#e11d48', 'bg' => '#fff1f2', 'border' => '#fecdd3'],
            ['label' => 'Izin',        'value' => $stat['izin'],                                                           'icon' => 'heroicon-o-clock',          'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
            ['label' => 'Sakit',       'value' => $stat['sakit'],                                                          'icon' => 'heroicon-o-heart',          'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
            ['label' => 'Rata² Skor', 'value' => $stat['rata_skor'] ? number_format($stat['rata_skor'], 1) : '—',         'icon' => 'heroicon-o-star',           'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
            ['label' => 'Sertifikat',  'value' => $stat['sertifikat'],                                                     'icon' => 'heroicon-o-document-check', 'color' => '#0d9488', 'bg' => '#f0fdfa', 'border' => '#99f6e4'],
        ] as $s)
            <div style="display:flex; flex-direction:column; justify-content:space-between; border-radius:1rem; background:#fff; padding:1rem; border:1.5px solid {{ $s['border'] }}; box-shadow:0 2px 6px rgba(0,0,0,0.04);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.25rem;">
                    <p style="font-size:0.5625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:{{ $s['color'] }}; line-height:1.3;">{{ $s['label'] }}</p>
                    <div style="display:flex; align-items:center; justify-content:center; width:1.5rem; height:1.5rem; border-radius:0.375rem; background:{{ $s['bg'] }}; flex-shrink:0;">
                        @svg($s['icon'], 'w-3.5 h-3.5', ['style' => 'color:' . $s['color'] . ';width:0.875rem;height:0.875rem;'])
                    </div>
                </div>
                <p style="font-size:1.75rem; font-weight:900; line-height:1; color:{{ $s['color'] }}; margin-top:0.5rem;">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Progress bar kehadiran --}}
    @if ($stat['total_sesi'] > 0)
        <div style="border-radius:1rem; background:#fff; padding:1rem 1.5rem; border:1.5px solid #a7f3d0; box-shadow:0 1px 6px rgba(5,150,105,0.06); margin-bottom:1.25rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                <p style="font-size:0.875rem; font-weight:700; color:#374151;">Tingkat Kehadiran</p>
                <p style="font-size:0.875rem; font-weight:900; color:#059669;">{{ $stat['persentase_hadir'] }}%</p>
            </div>
            <div style="height:0.75rem; width:100%; overflow:hidden; border-radius:9999px; background:#d1fae5;">
                <div style="height:100%; border-radius:9999px; width:{{ $stat['persentase_hadir'] }}%; background:linear-gradient(90deg,#059669,#34d399); transition:width 0.7s ease;"></div>
            </div>
            <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.375rem;">{{ $stat['hadir'] }} hadir dari {{ $stat['total_sesi'] }} sesi</p>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════
         2 KOLOM: DATA PRIBADI + JABATAN
    ══════════════════════════════════════════════════════ --}}
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; margin-bottom:1.25rem;">

        {{-- ── KOLOM KIRI: DATA PRIBADI ── --}}
        <div style="border-radius:1.25rem; background:#fff; overflow:hidden; border:1.5px solid #a7f3d0; box-shadow:0 1px 6px rgba(5,150,105,0.06);">

            {{-- Header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:linear-gradient(to right,#f0fdf4,#ecfdf5); border-bottom:1.5px solid #a7f3d0;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:#059669; flex-shrink:0;">
                        <x-heroicon-s-identification style="width:1rem; height:1rem; color:white;" />
                    </div>
                    <p style="font-size:0.875rem; font-weight:700; color:#1f2937;">Data Pribadi</p>
                </div>
                {{-- <button wire:click="prepareDataForForm"
                        style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.625rem; padding:0.375rem 0.75rem; font-size:0.75rem; font-weight:700; color:#059669; background:#d1fae5; border:1.5px solid #6ee7b7; cursor:pointer; transition:all 0.15s;"
                        onmouseover="this.style.background='#a7f3d0'" onmouseout="this.style.background='#d1fae5'">
                    <x-heroicon-o-pencil-square style="width:0.75rem; height:0.75rem;" />
                    Edit Profil
                </button> --}}
            </div>

            @foreach ([
                ['label' => 'NIK',              'value' => $anggota->nik,                                                          'mono' => true],
                ['label' => 'Jenis Kelamin',     'value' => $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
                ['label' => 'Tempat, Tgl Lahir', 'value' => $anggota->tempat_lahir . ', ' . optional($anggota->tanggal_lahir)->format('d/m/Y')],
                ['label' => 'Umur',              'value' => $anggota->umur ? $anggota->umur . ' tahun' : '—'],
                ['label' => 'Status Pernikahan', 'value' => $anggota->status_pernikahan ?? '—'],
                ['label' => 'Golongan Darah',    'value' => $anggota->golongan_darah ?? '—'],
                ['label' => 'Tinggi / Berat',    'value' => ($anggota->tinggi_badan ?? '—') . ' cm / ' . ($anggota->berat_badan ?? '—') . ' kg'],
                ['label' => 'Email',             'value' => $anggota->alamat_email ?? '—'],
                ['label' => 'Pekerjaan',         'value' => $anggota->pekerjaan?->nama_pekerjaan ?? '—'],
                ['label' => 'Afiliasi Politik',  'value' => $anggota->politik?->partai_politik ?? '—'],
                ['label' => 'NPWP',              'value' => $anggota->npwp_status ? ($anggota->npwp_nomor ?? 'Ada') : 'Tidak ada'],
                ['label' => 'BPJS',              'value' => $anggota->bpjs_status ? ($anggota->bpjs_nomor ?? 'Ada') : 'Tidak ada'],
                ['label' => 'Alamat',            'value' => 'RT ' . $anggota->rt . ' RW ' . $anggota->rw . ($anggota->alamat_lengkap ? ', ' . $anggota->alamat_lengkap : '')],
            ] as $i => $row)
                <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.625rem 1.25rem; {{ $i > 0 ? 'border-top:1px solid #ecfdf5;' : '' }}">
                    <p style="width:8.5rem; flex-shrink:0; font-size:0.75rem; font-weight:600; color:#9ca3af; padding-top:0.0625rem;">{{ $row['label'] }}</p>
                    <p style="flex:1; font-size:0.75rem; font-weight:700; color:#1f2937; {{ ($row['mono'] ?? false) ? 'font-family:monospace;' : '' }}">{{ $row['value'] }}</p>
                </div>
            @endforeach

        </div>


        {{-- ── KOLOM KANAN: JABATAN / STRUKTUR ORGANISASI ── --}}
        <div style="border-radius:1.25rem; background:#fff; overflow:hidden; border:1.5px solid #a7f3d0; box-shadow:0 1px 6px rgba(5,150,105,0.06);">

            <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:linear-gradient(to right,#f0fdf4,#ecfdf5); border-bottom:1.5px solid #a7f3d0;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:#059669; flex-shrink:0;">
                        <x-heroicon-s-building-office-2 style="width:1rem; height:1rem; color:white;" />
                    </div>
                    <div>
                        <p style="font-size:0.875rem; font-weight:700; color:#1f2937;">Jabatan & Struktur Organisasi</p>
                        <p style="font-size:0.75rem; font-weight:600; color:#059669;">{{ $anggota->strukturOrganisasi->count() }} jabatan tercatat</p>
                    </div>
                </div>
                {{-- <button wire:click="openCreateStrukturModal"
                        style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.75rem; padding:0.5rem 1rem; font-size:0.75rem; font-weight:700; color:#fff; background:linear-gradient(135deg,#059669,#34d399); box-shadow:0 2px 8px rgba(5,150,105,0.28); border:none; cursor:pointer; transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <x-heroicon-o-plus style="width:0.875rem; height:0.875rem;" />
                    Tambah
                </button> --}}
            </div>

            @if ($anggota->strukturOrganisasi->isEmpty())
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.5rem; padding:4rem 2rem;">
                    <x-heroicon-o-building-office-2 style="width:2rem; height:2rem; color:#e5e7eb;" />
                    <p style="font-size:0.75rem; color:#9ca3af;">Belum ada jabatan tercatat</p>
                </div>
            @else
                @foreach ($anggota->strukturOrganisasi->sortByDesc('is_active') as $i => $so)
                    <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:1rem 1.25rem; {{ $i > 0 ? 'border-top:1px solid #ecfdf5;' : '' }}">
                        <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.625rem; flex-shrink:0; margin-top:0.125rem; background:{{ $so->is_active ? '#d1fae5' : '#f3f4f6' }};">
                            @if ($so->is_active)
                                <x-heroicon-s-check-circle style="width:1rem; height:1rem; color:#059669;" />
                            @else
                                <x-heroicon-s-clock style="width:1rem; height:1rem; color:#9ca3af;" />
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                <p style="font-size:0.875rem; font-weight:800; color:#1f2937;">{{ $so->jabatan?->nama_jabatan ?? '—' }}</p>
                                @if ($so->is_active)
                                    <span style="border-radius:9999px; padding:0.125rem 0.5rem; font-size:0.625rem; font-weight:700; color:#065f46; background:#d1fae5; border:1px solid #6ee7b7;">Aktif</span>
                                @endif
                                {{-- Badge tipe --}}
                                @if ($so->tipe_organisasi === 'internal')
                                    <span style="border-radius:9999px; padding:0.125rem 0.5rem; font-size:0.625rem; font-weight:700; color:#1e40af; background:#dbeafe; border:1px solid #93c5fd;">Internal</span>
                                @else
                                    <span style="border-radius:9999px; padding:0.125rem 0.5rem; font-size:0.625rem; font-weight:700; color:#92400e; background:#fef3c7; border:1px solid #fcd34d;">Eksternal</span>
                                @endif
                            </div>
                            {{-- Info level / organisasi --}}
                            @if ($so->tipe_organisasi === 'internal')
                                <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.125rem;">
                                    {{ $so->level?->level_type ?? '' }}{{ $so->level?->nama_level ? ' — ' . $so->level->nama_level : '' }}
                                </p>
                            @else
                                <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.125rem;">
                                    {{ $so->organisasi?->nama_organisasi ?? $so->nama_organisasi ?? '—' }}
                                </p>
                            @endif
                            <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.25rem;">
                                {{ optional($so->masa_khidmat_mulai)->format('d/m/Y') }} – {{ $so->masa_khidmat_selesai ? optional($so->masa_khidmat_selesai)->format('d/m/Y') : 'Sekarang' }}
                            </p>
                        </div>
                        {{-- Aksi --}}
                        <div style="display:flex; align-items:center; gap:0.25rem; flex-shrink:0;">
                            <button wire:click="openEditStrukturModal({{ $so->id }})"
                                    style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                    onmouseover="this.style.background='#d1fae5';this.style.color='#059669'"
                                    onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                <x-heroicon-o-pencil-square style="width:1rem; height:1rem;" />
                            </button>
                            <button wire:click="deleteStruktur({{ $so->id }})"
                                    wire:confirm="Yakin ingin menghapus jabatan ini?"
                                    style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                    onmouseover="this.style.background='#fee2e2';this.style.color='#e11d48'"
                                    onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                <x-heroicon-o-trash style="width:1rem; height:1rem;" />
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════
         RIWAYAT PENDIDIKAN
    ══════════════════════════════════════════════════════ --}}
    <div style="border-radius:1.25rem; background:#fff; overflow:hidden; border:1.5px solid #bfdbfe; box-shadow:0 1px 6px rgba(37,99,235,0.06); margin-bottom:1.25rem;">

        <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:linear-gradient(to right,#eff6ff,#dbeafe); border-bottom:1.5px solid #bfdbfe;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:#059669; flex-shrink:0;">
                    <x-heroicon-s-academic-cap style="width:1rem; height:1rem; color:white;" />
                </div>
                <div>
                    <p style="font-size:0.875rem; font-weight:700; color:#1f2937;">Riwayat Pendidikan</p>
                    <p style="font-size:0.75rem; font-weight:600; color:#059669;">{{ $anggota->pendidikans->count() }} data tercatat</p>
                </div>
            </div>
            {{-- <button wire:click="openCreatePendidikanModal"
                    style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.75rem; padding:0.5rem 1rem; font-size:0.75rem; font-weight:700; color:#fff; background:linear-gradient(135deg,#047551,#059669); box-shadow:0 2px 8px rgba(124,58,237,0.28); border:none; cursor:pointer; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <x-heroicon-o-plus style="width:0.875rem; height:0.875rem;" />
                Tambah Pendidikan
            </button> --}}
        </div>

        @if ($anggota->pendidikans->isEmpty())
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.625rem; padding:4rem 2rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:3.5rem; height:3.5rem; border-radius:1rem; background:#dbeafe;">
                    <x-heroicon-o-academic-cap style="width:1.75rem; height:1.75rem; color:#059669;" />
                </div>
                <p style="font-size:0.875rem; font-weight:600; color:#6b7280;">Belum ada riwayat pendidikan</p>
                <p style="font-size:0.75rem; color:#9ca3af;">Klik "Tambah Pendidikan" untuk memulai</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                    <thead>
                        <tr style="background:#eff6ff; border-bottom:1.5px solid #bfdbfe;">
                            @foreach (['Jenjang', 'Nama Institusi', 'Jurusan', 'Tahun', 'Status', ''] as $th)
                                <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#059669; white-space:nowrap;">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($anggota->pendidikans->sortByDesc('tahun_masuk') as $p)
                            @php
                                [$sBg, $sText, $sBorder] = match($p->status ?? '') {
                                    'Lulus'           => ['#d1fae5', '#065f46', '#6ee7b7'],
                                    'Tidak Lulus'     => ['#fee2e2', '#9f1239', '#fca5a5'],
                                    'Sedang Berjalan' => ['#fef9c3', '#854d0e', '#fcd34d'],
                                    default           => ['#f3f4f6', '#374151', '#d1d5db'],
                                };
                            @endphp
                            <tr style="border-bottom:1px solid #eff6ff;"
                                onmouseover="this.style.background='#eff6ff'"
                                onmouseout="this.style.background=''">
                                <td style="padding:0.875rem 1.25rem;">
                                    <span style="display:inline-flex; align-items:center; border-radius:0.5rem; padding:0.25rem 0.625rem; font-size:0.75rem; font-weight:700; background:#dbeafe; color:#059669; border:1px solid #93c5fd;">
                                        {{ $p->jenjang }}
                                    </span>
                                </td>
                                <td style="padding:0.875rem 1.25rem;">
                                    <p style="font-weight:600; color:#111827;">{{ $p->nama_institusi }}</p>
                                </td>
                                <td style="padding:0.875rem 1.25rem; color:#6b7280; font-size:0.8125rem;">
                                    {{ $p->jurusan ?? '—' }}
                                </td>
                                <td style="padding:0.875rem 1.25rem; white-space:nowrap; color:#374151; font-size:0.8125rem;">
                                    {{ $p->tahun_masuk }} – {{ $p->tahun_lulus ?? 'Sekarang' }}
                                </td>
                                <td style="padding:0.875rem 1.25rem;">
                                    <span style="display:inline-flex; align-items:center; border-radius:9999px; padding:0.2rem 0.625rem; font-size:0.75rem; font-weight:700; background:{{ $sBg }}; color:{{ $sText }}; border:1.5px solid {{ $sBorder }};">
                                        {{ $p->status ?? '—' }}
                                    </span>
                                </td>
                                <td style="padding:0.875rem 1.25rem;">
                                    <div style="display:flex; align-items:center; justify-content:flex-end; gap:0.25rem;">
                                        <button wire:click="openEditPendidikanModal({{ $p->id }})"
                                                style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                                onmouseover="this.style.background='#dbeafe';this.style.color='#2563eb'"
                                                onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                            <x-heroicon-o-pencil-square style="width:1rem; height:1rem;" />
                                        </button>
                                        <button wire:click="deletePendidikan({{ $p->id }})"
                                                wire:confirm="Yakin ingin menghapus data pendidikan ini?"
                                                style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                                onmouseover="this.style.background='#fee2e2';this.style.color='#e11d48'"
                                                onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                            <x-heroicon-o-trash style="width:1rem; height:1rem;" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════
         SOCIAL MEDIA
    ══════════════════════════════════════════════════════ --}}
    <div style="border-radius:1.25rem; background:#fff; overflow:hidden; border:1.5px solid #e9d5ff; box-shadow:0 1px 6px rgba(124,58,237,0.06); margin-bottom:1.25rem;">

        <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:linear-gradient(to right,#faf5ff,#f3e8ff); border-bottom:1.5px solid #e9d5ff;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:#059669; flex-shrink:0;">
                    <x-heroicon-s-computer-desktop style="width:1rem; height:1rem; color:white;" />
                </div>
                <div>
                    <p style="font-size:0.875rem; font-weight:700; color:#1f2937;">Social Media</p>
                    <p style="font-size:0.75rem; font-weight:600; color:#059669;">{{ $anggota->socialMediaAccounts->count() }} akun terdaftar</p>
                </div>
            </div>
            {{-- <button wire:click="openCreateSosmedModal"
                    style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.75rem; padding:0.5rem 1rem; font-size:0.75rem; font-weight:700; color:#fff; background:linear-gradient(135deg,#047551,#059669); box-shadow:0 2px 8px rgba(124,58,237,0.28); border:none; cursor:pointer; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <x-heroicon-o-plus style="width:0.875rem; height:0.875rem;" />
                Tambah Akun
            </button> --}}
        </div>

        @if ($anggota->socialMediaAccounts->isEmpty())
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.625rem; padding:4rem 2rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:3.5rem; height:3.5rem; border-radius:1rem; background:#f3e8ff;">
                    <x-heroicon-o-computer-desktop style="width:1.75rem; height:1.75rem; color:#059669;" />
                </div>
                <p style="font-size:0.875rem; font-weight:600; color:#6b7280;">Belum ada akun social media</p>
                <p style="font-size:0.75rem; color:#9ca3af;">Klik "Tambah Akun" untuk memulai</p>
            </div>
        @else
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1rem; padding:1.25rem;">
                @foreach ($anggota->socialMediaAccounts as $sm)
                    <div style="border-radius:0.875rem; border:1.5px solid #f3e8ff; padding:1rem; display:flex; align-items:center; gap:0.875rem; background:#faf5ff;">
                        <div style="display:flex; align-items:center; justify-content:center; width:2.5rem; height:2.5rem; border-radius:0.625rem; background:#ede9fe; flex-shrink:0;">
                            <x-heroicon-o-at-symbol style="width:1.25rem; height:1.25rem; color:#7c3aed;" />
                        </div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#7c3aed;">{{ $sm->socialMedia?->platform_name ?? '—' }}</p>
                            <p style="font-size:0.875rem; font-weight:600; color:#1f2937; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $sm->username ?? '—' }}</p>
                            @if ($sm->url)
                                <a href="{{ $sm->url }}" target="_blank"
                                   style="font-size:0.75rem; color:#7c3aed; text-decoration:none; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block;"
                                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                    {{ $sm->url }}
                                </a>
                            @endif
                        </div>
                        <div style="display:flex; flex-direction:column; gap:0.25rem; flex-shrink:0;">
                            <button wire:click="openEditSosmedModal({{ $sm->id }})"
                                    style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                    onmouseover="this.style.background='#ede9fe';this.style.color='#7c3aed'"
                                    onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                <x-heroicon-o-pencil-square style="width:1rem; height:1rem;" />
                            </button>
                            <button wire:click="deleteSosmed({{ $sm->id }})"
                                    wire:confirm="Yakin ingin menghapus akun ini?"
                                    style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                    onmouseover="this.style.background='#fee2e2';this.style.color='#e11d48'"
                                    onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                <x-heroicon-o-trash style="width:1rem; height:1rem;" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════
         PREVIEW KTA  (tidak diubah)
    ══════════════════════════════════════════════════════ --}}
    @php
        $ktaPrintLog   = $this->getKtaPrintLog();
        $ktaTemplate   = $this->getKtaTemplate();
        $bgBase64      = $this->toBase64($ktaTemplate?->image);
        $fotoAnggota   = $this->toBase64($anggota->foto);
        $ttdKetua      = $this->toBase64($ktaTemplate?->ttd_ketua);
        $ttdSekretaris = $this->toBase64($ktaTemplate?->ttd_sekretaris);
        $jabatanAktif  = $anggota->strukturOrganisasi->where('is_active', true)->first();
        $isKtaValid    = $ktaTemplate && $ktaTemplate->is_active && $ktaTemplate->tanggal_berlaku_sampai >= now();
    @endphp

    <div style="border-radius:1.25rem; background:#fff; overflow:hidden; border:1.5px solid #a7f3d0; box-shadow:0 1px 6px rgba(5,150,105,0.06); margin-bottom:1.25rem;">

        {{-- Header panel --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:linear-gradient(to right,#f0fdf4,#ecfdf5); border-bottom:1.5px solid #a7f3d0;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:#059669; flex-shrink:0;">
                    <x-heroicon-s-identification style="width:1rem; height:1rem; color:white;" />
                </div>
                <div>
                    <p style="font-size:0.875rem; font-weight:700; color:#1f2937;">Preview Kartu Tanda Anggota (KTA)</p>
                    <p style="font-size:0.75rem; color:#059669; font-weight:600;">
                        @if ($ktaTemplate)
                            Batch: {{ $ktaTemplate->nama_batch ?? '—' }}
                            &nbsp;·&nbsp;
                            No. KTA: {{ $anggota->nia ?? '—' }}
                        @else
                            Belum ada data KTA
                        @endif
                    </p>
                </div>
            </div>

            @if ($ktaPrintLog)
                <a href="{{ $this->getKtaDownloadUrl() }}"
                   target="_blank"
                   style="display:inline-flex; align-items:center; gap:0.5rem; border-radius:0.75rem; padding:0.5rem 1.125rem; font-size:0.8125rem; font-weight:700; color:#fff; background:linear-gradient(135deg,#059669,#34d399); box-shadow:0 2px 8px rgba(5,150,105,0.3); border:none; text-decoration:none; transition:opacity 0.15s;"
                   onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                    <x-heroicon-o-arrow-down-tray style="width:1rem; height:1rem;" />
                    Download PDF
                </a>
            @endif
        </div>

        @if (! $ktaTemplate)
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.625rem; padding:4rem 2rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:3.5rem; height:3.5rem; border-radius:1rem; background:#d1fae5;">
                    <x-heroicon-o-identification style="width:1.75rem; height:1.75rem; color:#059669;" />
                </div>
                <p style="font-size:0.875rem; font-weight:600; color:#6b7280;">Belum ada KTA untuk anggota ini</p>
                <p style="font-size:0.75rem; color:#9ca3af;">KTA akan muncul setelah data di verifikasi oleh admin</p>
            </div>
        @else

            <div style="display:flex; align-items:center; justify-content:center; padding:2.5rem 2rem; background:#e5e7eb;">
                <div style="position:relative; width:680px; height:415px; border-radius:0.75rem; overflow:hidden; box-shadow:0 24px 64px rgba(0,0,0,0.28), 0 4px 16px rgba(0,0,0,0.14); flex-shrink:0; font-family:Arial,sans-serif;">
                    @if ($bgBase64)
                        <img src="{{ $bgBase64 }}" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:0;" />
                    @else
                        <div style="position:absolute; inset:0; background:linear-gradient(135deg,#064e3b 0%,#065f46 40%,#059669 75%,#10b981 100%); z-index:0;"></div>
                    @endif

                    <div style="position:absolute; top:75px; left:0; right:0; bottom:0; z-index:10;">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="width:189px; vertical-align:top; padding:15px 8px 15px 45px;">
                                    <div style="width:143px; height:188px; border:3px solid #1b5e20; overflow:hidden; background:rgba(255,255,255,0.5); border-radius:2px;">
                                        @if ($fotoAnggota)
                                            <img src="{{ $fotoAnggota }}" style="width:100%; height:100%; display:block; object-fit:cover;" />
                                        @else
                                            <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.25rem; background:rgba(255,255,255,0.2);">
                                                <x-heroicon-o-user style="width:3rem; height:3rem; color:rgba(255,255,255,0.5);" />
                                                <p style="font-size:0.6rem; color:rgba(255,255,255,0.5); font-weight:600;">Foto</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td style="vertical-align:top; padding:15px 15px 8px 0;">
                                    <p style="font-size:16pt; font-weight:900; color:#111; margin-bottom:11px; line-height:1;">KARTU REGISTRASI</p>
                                    <table style="width:100%; border-collapse:collapse;">
                                        @php
                                            $ktaRows = [
                                                ['lbl' => 'Nama',          'val' => $anggota->nama_lengkap ?? '—'],
                                                ['lbl' => 'No. Registrasi','val' => $anggota->nia ?? '—'],
                                                ['lbl' => 'Kecamatan',     'val' => $anggota->kecamatan?->nama_kecamatan ?? '—'],
                                                ['lbl' => 'Desa/Kel.',     'val' => $anggota->desa?->nama_desa ?? '—'],
                                                ['lbl' => 'Keanggotaan',   'val' => 'Kader'],
                                            ];
                                            if ($ktaTemplate->tanggal_berlaku_sampai) {
                                                $ktaRows[] = ['lbl' => 'Masa Berlaku', 'val' => $ktaTemplate->tanggal_berlaku_sampai->format('d/m/Y')];
                                            }
                                        @endphp
                                        @foreach ($ktaRows as $row)
                                            <tr>
                                                <td style="font-size:9.5pt; font-weight:bold; color:#111; width:120px; white-space:nowrap; padding:3px 0; vertical-align:top; line-height:1.2;">{{ $row['lbl'] }}</td>
                                                <td style="font-size:9.5pt; color:#111; width:16px; padding:3px 0; vertical-align:top; line-height:1.2;">:</td>
                                                <td style="font-size:9.5pt; color:#111; padding:3px 0; vertical-align:top; line-height:1.2;">{{ $row['val'] }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                    <div style="margin-top:11px; text-align:center;">
                                        <p style="font-size:8.5pt; font-weight:bold; color:#1b5e20; line-height:1.3;">PIMPINAN CABANG<br>GP. ANSOR KAB. KUDUS</p>
                                        <table style="width:100%; border-collapse:collapse;">
                                            <tr>
                                                <td style="text-align:center; font-size:8.5pt; padding:0 8px; vertical-align:bottom; width:50%;">
                                                    <span style="display:block; font-weight:bold; color:#111; text-transform:uppercase;">KETUA</span>
                                                    <span style="display:block; height:45px; text-align:center; line-height:45px;">
                                                        @if ($ttdKetua)
                                                            <img src="{{ $ttdKetua }}" style="max-height:60px; max-width:100%; object-fit:contain; display:inline-block;" />
                                                        @endif
                                                    </span>
                                                    <span style="display:block; font-size:8.5pt; font-weight:bold; color:#111; padding-top:1px; margin-top:1px;">{{ $ktaTemplate->nama_ketua ?? 'Ketua' }}</span>
                                                </td>
                                                <td style="text-align:center; font-size:8.5pt; padding:0 8px; vertical-align:bottom; width:50%;">
                                                    <span style="display:block; font-weight:bold; color:#111; text-transform:uppercase;">SEKRETARIS</span>
                                                    <span style="display:block; height:45px; text-align:center; line-height:45px;">
                                                        @if ($ttdSekretaris)
                                                            <img src="{{ $ttdSekretaris }}" style="max-height:60px; max-width:100%; object-fit:contain; display:inline-block;" />
                                                        @endif
                                                    </span>
                                                    <span style="display:block; font-size:8.5pt; font-weight:bold; color:#111; padding-top:1px; margin-top:1px;">{{ $ktaTemplate->nama_sekretaris ?? 'Sekretaris' }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="position:absolute; bottom:12px; right:14px; z-index:20;">
                        <span style="display:inline-flex; align-items:center; gap:0.25rem; border-radius:9999px; padding:0.2rem 0.6rem; font-size:0.55rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; background:{{ $isKtaValid ? 'rgba(52,211,153,0.25)' : 'rgba(248,113,113,0.2)' }}; color:{{ $isKtaValid ? '#6ee7b7' : '#fca5a5' }}; border:1px solid {{ $isKtaValid ? 'rgba(52,211,153,0.5)' : 'rgba(248,113,113,0.4)' }};">
                            {{ $isKtaValid ? '✓ Aktif' : '✗ Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0; border-top:1.5px solid #ecfdf5;">
                @foreach ([
                    ['label' => 'Nama Batch',     'value' => $ktaTemplate->nama_batch ?? '—',                                                 'icon' => 'heroicon-o-rectangle-stack'],
                    ['label' => 'Tanggal Terbit', 'value' => $ktaTemplate->tanggal_terbit?->format('d/m/Y') ?? '—',                           'icon' => 'heroicon-o-calendar-days'],
                    ['label' => 'Berlaku Sampai', 'value' => $ktaTemplate->tanggal_berlaku_sampai?->format('d/m/Y') ?? '—',                   'icon' => 'heroicon-o-clock'],
                    ['label' => 'Status',         'value' => $isKtaValid ? 'Aktif' : ($ktaTemplate->is_active ? 'Kadaluarsa' : 'Nonaktif'),   'icon' => 'heroicon-o-check-badge'],
                ] as $i => $info)
                    <div style="padding:0.875rem 1.25rem; {{ $i > 0 ? 'border-left:1.5px solid #ecfdf5;' : '' }}">
                        <div style="display:flex; align-items:center; gap:0.375rem; margin-bottom:0.25rem;">
                            @svg($info['icon'], 'w-3 h-3', ['style' => 'color:#059669;width:0.75rem;height:0.75rem;flex-shrink:0;'])
                            <p style="font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af;">{{ $info['label'] }}</p>
                        </div>
                        <p style="font-size:0.875rem; font-weight:800; color:#1f2937;">{{ $info['value'] }}</p>
                    </div>
                @endforeach
            </div>

            @if (! $ktaPrintLog)
                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1.25rem; background:#fffbeb; border-top:1.5px solid #fde68a;">
                    <x-heroicon-o-exclamation-triangle style="width:1rem; height:1rem; color:#d97706; flex-shrink:0;" />
                    <p style="font-size:0.75rem; color:#92400e; font-weight:600;">
                        Data belum bisa di cetak karena belum di verifikasi oleh admin. Mohon tunggu atau hubungi admin untuk informasi lebih lanjut.
                    </p>
                </div>
            @endif
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════
         PREVIEW SERTIFIKAT  (tidak diubah)
    ══════════════════════════════════════════════════════ --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cookie&family=Felipa&display=swap" rel="stylesheet">

    @php
        $sertPrintLogs   = $this->getSertifikatPrintLogs();
        $sertPreview     = $this->getSertifikatPreviewData();
        $sertDownloadUrl = $this->getSertifikatDownloadUrl();

        $latestPrintLog = $sertPreview['printLog'];
        $sertPelatihan  = $sertPreview['pelatihan'];
        $sertDetail     = $sertPreview['detail'];
        $sertTemplate   = $sertPreview['template'];
        $sertMateriList = $sertPreview['materiList'];

        $sertBgB64    = $this->toBase64($sertTemplate?->image ?? null);
        $sertTtdKetua = $this->toBase64($sertTemplate?->ttd_ketua ?? null);
        $sertTtdSek   = $this->toBase64($sertTemplate?->ttd_sekretaris ?? null);

        $hasSertifikat = $latestPrintLog !== null;

        $S = 0.8013;
    @endphp

    <div style="border-radius:1.25rem; background:#fff; overflow:hidden; border:1.5px solid #99f6e4; box-shadow:0 1px 6px rgba(13,148,136,0.08); margin-bottom:1.25rem;">

        {{-- Header panel --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:linear-gradient(to right,#f0fdfa,#ccfbf1); border-bottom:1.5px solid #99f6e4;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:#0d9488; flex-shrink:0;">
                    <x-heroicon-s-document-check style="width:1rem; height:1rem; color:white;" />
                </div>
                <div>
                    <p style="font-size:0.875rem; font-weight:700; color:#1f2937;">Preview Sertifikat</p>
                    <p style="font-size:0.75rem; color:#0d9488; font-weight:600;">
                        @if ($hasSertifikat)
                            {{ $sertPrintLogs->count() }} sertifikat dicetak
                            &nbsp;·&nbsp;
                            Pelatihan: {{ $sertPelatihan?->nama_pelatihan ?? '—' }}
                        @else
                            Belum ada sertifikat di Log Cetak
                        @endif
                    </p>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:0.625rem;">
                @if ($sertPrintLogs->count() > 1)
                    <span style="display:inline-flex; align-items:center; gap:0.25rem; border-radius:9999px; padding:0.2rem 0.625rem; font-size:0.7rem; font-weight:600; color:#0d9488; background:#ccfbf1; border:1px solid #99f6e4;">
                        <x-heroicon-o-document-duplicate style="width:0.75rem; height:0.75rem;" />
                        Menampilkan yang terbaru dari {{ $sertPrintLogs->count() }}
                    </span>
                @endif

                @if ($hasSertifikat)
                    <a href="{{ $sertDownloadUrl }}"
                       target="_blank"
                       style="display:inline-flex; align-items:center; gap:0.5rem; border-radius:0.75rem; padding:0.5rem 1.125rem; font-size:0.8125rem; font-weight:700; color:#fff; background:linear-gradient(135deg,#0d9488,#2dd4bf); box-shadow:0 2px 8px rgba(13,148,136,0.3); border:none; text-decoration:none; transition:opacity 0.15s;"
                       onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                        <x-heroicon-o-arrow-down-tray style="width:1rem; height:1rem;" />
                        Download PDF
                    </a>
                @endif
            </div>
        </div>

        @if (! $hasSertifikat)
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.625rem; padding:4rem 2rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:3.5rem; height:3.5rem; border-radius:1rem; background:#ccfbf1;">
                    <x-heroicon-o-document-check style="width:1.75rem; height:1.75rem; color:#0d9488;" />
                </div>
                <p style="font-size:0.875rem; font-weight:600; color:#6b7280;">Belum ada sertifikat untuk anggota ini</p>
                <p style="font-size:0.75rem; color:#9ca3af;">Sertifikat akan muncul setelah dicetak melalui menu Log Cetak</p>
            </div>

        @else

            {{-- HALAMAN DEPAN --}}
            <div style="padding:2rem; background:#e5e7eb; border-bottom:1.5px solid #99f6e4;">

                <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem; margin-bottom:1rem;">
                    <div style="height:1px; flex:1; background:linear-gradient(to right,transparent,#d1d5db);"></div>
                    <span style="font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280; background:#e5e7eb; padding:0 0.75rem;">Halaman Depan</span>
                    <div style="height:1px; flex:1; background:linear-gradient(to left,transparent,#d1d5db);"></div>
                </div>

                <div style="display:flex; justify-content:center; overflow-x:auto;">
                    <div style="position:relative; width:900px; height:636px; overflow:hidden; flex-shrink:0; font-family:Arial,sans-serif; font-size:14px; border-radius:0.75rem; box-shadow:0 24px 64px rgba(0,0,0,0.28),0 4px 16px rgba(0,0,0,0.14); background:#fff;">

                        @if ($sertBgB64)
                            <img src="{{ $sertBgB64 }}" style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; z-index:0;" />
                        @else
                            <div style="position:absolute; top:0; left:0; right:0; bottom:0; z-index:0; background:linear-gradient(135deg,#e8f5e9 0%,#c8e6c9 25%,#ffffff 60%,#f1f8e9 100%);"></div>
                            <div style="position:absolute; top:10px; left:10px; right:10px; bottom:10px; border:2px solid #2d6a2d; border-radius:4px; z-index:1; pointer-events:none;"></div>
                            <div style="position:absolute; top:16px; left:16px; right:16px; bottom:16px; border:1px solid rgba(45,106,45,0.25); border-radius:3px; z-index:1; pointer-events:none;"></div>
                        @endif

                        <div style="position:absolute; top:9px; left:91px; right:91px; height:448px; z-index:10; display:flex; flex-direction:column; align-items:center; justify-content:flex-start; text-align:center; overflow:hidden;">
                            <div style="font-family:'Cookie',cursive; font-size:32px; font-weight:900; color:#1a4a1a; letter-spacing:8px; margin-top:151px; margin-bottom:2px; line-height:1;">SERTIFIKAT</div>
                            <div style="font-size:14px; color:#555; margin-bottom:5px;">Diberikan kepada:</div>
                            <div style="font-family:'Felipa',cursive; font-size:36px; color:#2d6a2d; font-style:italic; border-bottom:2px solid #2d6a2d; padding-bottom:2px; margin-bottom:3px; min-width:200px; display:inline-block; line-height:1.1;">{{ $anggota->nama_lengkap ?? '...' }}</div>
                            <div style="font-size:14px; color:#444; margin:4px 0; line-height:1.5;">
                                {{ $anggota->nia ?? '' }}
                                @if(!empty($anggota->nia) && !empty($anggota->alamat))<br>@endif
                                {{ $anggota->alamat ?? '' }}
                            </div>
                            <div style="font-size:13px; color:#222; margin:4px 0; line-height:1.7;">
                                Sebagai penghargaan atas partisipasinya dalam<br>
                                <span style="font-size:20px; font-weight:bold; color:#2d6a2d;">{{ $sertPelatihan?->nama_pelatihan ?? '...' }}</span><br>
                                yang berlangsung pada
                                {{ $sertDetail?->tanggal ? \Carbon\Carbon::parse($sertDetail->tanggal)->translatedFormat('d F Y') : '...' }}
                                di {{ $sertDetail?->tempat ?? '...' }}
                            </div>
                            <div style="font-size:13px; font-weight:bold; color:#2d6a2d; margin:4px 0 0 0; line-height:1.5;">
                                Pengurus Cabang<br>
                                Gerakan Pemuda Ansor Kabupaten Kudus
                            </div>
                        </div>

                        <div style="position:absolute; top:469px; left:0; right:0; bottom:0; z-index:10; display:table; table-layout:fixed; width:100%; padding:0 61px;">
                            <div style="display:table-cell; text-align:center; vertical-align:top; width:50%; padding:0 30px;">
                                @if ($sertTtdKetua)
                                    <img src="{{ $sertTtdKetua }}" style="height:72px; width:auto; max-width:100%; display:block; margin:0 auto 2px auto; object-fit:contain;" />
                                @else
                                    <div style="height:36px;"></div>
                                @endif
                                <div>
                                    <div style="font-size:14px; font-weight:bold; color:#1a4a1a;">{{ $sertTemplate?->nama_ketua ?? '...' }}</div>
                                    <div style="font-size:14px; color:#555;">Ketua</div>
                                </div>
                            </div>
                            <div style="display:table-cell; text-align:center; vertical-align:top; width:50%; padding:0 30px;">
                                @if ($sertTtdSek)
                                    <img src="{{ $sertTtdSek }}" style="height:72px; width:auto; max-width:100%; display:block; margin:0 auto 2px auto; object-fit:contain;" />
                                @else
                                    <div style="height:36px;"></div>
                                @endif
                                <div>
                                    <div style="font-size:14px; font-weight:bold; color:#1a4a1a;">{{ $sertTemplate?->nama_sekretaris ?? '...' }}</div>
                                    <div style="font-size:14px; color:#555;">Sekretaris</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- HALAMAN BELAKANG --}}
            <div style="padding:2rem; background:#f0fdfa; border-bottom:1.5px solid #99f6e4;">

                <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem; margin-bottom:1rem;">
                    <div style="height:1px; flex:1; background:linear-gradient(to right,transparent,#d1d5db);"></div>
                    <span style="font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280; background:#f0fdfa; padding:0 0.75rem;">Halaman Belakang</span>
                    <div style="height:1px; flex:1; background:linear-gradient(to left,transparent,#d1d5db);"></div>
                </div>

                <div style="display:flex; justify-content:center; overflow-x:auto;">
                    <div style="width:900px; flex-shrink:0; background:#ffffff; font-family:Arial,sans-serif; font-size:14px; border-radius:0.75rem; box-shadow:0 24px 64px rgba(0,0,0,0.22),0 4px 16px rgba(0,0,0,0.1); padding:21px 36px 18px 36px;">

                        <div style="font-size:12px; font-weight:bold; text-align:center; color:#1a4a1a; letter-spacing:2px; text-transform:uppercase; padding:4px 0 5px 0; margin-bottom:7px; border-bottom:2.5px solid #2d6a2d;">
                            {{ strtoupper($sertPelatihan?->nama_pelatihan ?? 'Pelatihan') }}
                        </div>

                        <table style="width:100%; border-collapse:collapse; font-size:10.5px; color:#222; margin-bottom:6px;">
                            <tr>
                                <td style="white-space:nowrap; font-weight:bold; color:#2d6a2d; width:115px; padding:2px 3px; vertical-align:top; line-height:1.65;">Nomor Registrasi</td>
                                <td style="white-space:nowrap; width:10px; padding:2px 5px; vertical-align:top; line-height:1.65;">:</td>
                                <td style="color:#333; padding:2px 3px; vertical-align:top; line-height:1.65;">{{ $anggota->nomor_registrasi ?? $anggota->nia ?? '...' }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-weight:bold; color:#2d6a2d; width:115px; padding:2px 3px; vertical-align:top; line-height:1.65;">Nama Peserta</td>
                                <td style="white-space:nowrap; width:10px; padding:2px 5px; vertical-align:top; line-height:1.65;">:</td>
                                <td style="color:#333; padding:2px 3px; vertical-align:top; line-height:1.65;">{{ $anggota->nama_lengkap ?? '...' }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-weight:bold; color:#2d6a2d; width:115px; padding:2px 3px; vertical-align:top; line-height:1.65;">Tempat, Tanggal Lahir</td>
                                <td style="white-space:nowrap; width:10px; padding:2px 5px; vertical-align:top; line-height:1.65;">:</td>
                                <td style="color:#333; padding:2px 3px; vertical-align:top; line-height:1.65;">
                                    {{ $anggota->tempat_lahir ?? '...' }},
                                    {{ $anggota->tanggal_lahir ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->translatedFormat('d F Y') : '...' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-weight:bold; color:#2d6a2d; width:115px; padding:2px 3px; vertical-align:top; line-height:1.65;">Utusan</td>
                                <td style="white-space:nowrap; width:10px; padding:2px 5px; vertical-align:top; line-height:1.65;">:</td>
                                <td style="color:#333; padding:2px 3px; vertical-align:top; line-height:1.65;">{{ optional($anggota->kecamatan)->nama_kecamatan ?? $anggota->kecamatan ?? '...' }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap; font-weight:bold; color:#2d6a2d; width:115px; padding:2px 3px; vertical-align:top; line-height:1.65;">Alamat</td>
                                <td style="white-space:nowrap; width:10px; padding:2px 5px; vertical-align:top; line-height:1.65;">:</td>
                                <td style="color:#333; padding:2px 3px; vertical-align:top; line-height:1.65;">{{ $anggota->alamat_lengkap ?? '...' }}</td>
                            </tr>
                        </table>

                        <hr style="border:none; border-top:1px solid #d5e8d5; margin:6px 0 5px 0;" />

                        <table style="width:100%; border-collapse:collapse; font-size:10.5px; table-layout:fixed; margin-bottom:6px;">
                            <colgroup>
                                <col style="width:5%">
                                <col style="width:47%">
                                <col style="width:16%">
                                <col style="width:32%">
                            </colgroup>
                            <thead>
                                <tr style="background-color:#1e5c1e;">
                                    <th style="padding:6px 8px; text-align:center; font-weight:bold; font-size:10.5px; color:#ffffff; letter-spacing:0.3px; border:1px solid #1e5c1e;">NO.</th>
                                    <th style="padding:6px 8px; text-align:center; font-weight:bold; font-size:10.5px; color:#ffffff; letter-spacing:0.3px; border:1px solid #1e5c1e;">MATERI {{ strtoupper($sertPelatihan?->nama_pelatihan ?? 'PELATIHAN') }}</th>
                                    <th style="padding:6px 8px; text-align:center; font-weight:bold; font-size:10.5px; color:#ffffff; letter-spacing:0.3px; border:1px solid #1e5c1e;">DURASI</th>
                                    <th style="padding:6px 8px; text-align:center; font-weight:bold; font-size:10.5px; color:#ffffff; letter-spacing:0.3px; border:1px solid #1e5c1e;">INSTRUKTUR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalMenit = 0; $rowNum = 0; @endphp
                                @forelse ($sertMateriList as $index => $sesi)
                                    @php
                                        $menit = 0;
                                        if ($sesi->jam_mulai && $sesi->jam_selesai) {
                                            $menit = abs(\Carbon\Carbon::parse($sesi->jam_mulai)->diffInMinutes(\Carbon\Carbon::parse($sesi->jam_selesai)));
                                        }
                                        $totalMenit += $menit;
                                        $rowNum++;
                                        $isEven = ($rowNum % 2 === 0);
                                    @endphp
                                    <tr style="{{ $isEven ? 'background-color:#f0f7f0;' : '' }}">
                                        <td style="border-bottom:1px solid #e0e0e0; border-left:1px solid #e8e8e8; border-right:1px solid #e8e8e8; border-top:none; padding:5px 8px; font-size:10.5px; vertical-align:middle; color:#555; text-align:center;">{{ $index + 1 }}</td>
                                        <td style="border-bottom:1px solid #e0e0e0; border-left:1px solid #e8e8e8; border-right:1px solid #e8e8e8; border-top:none; padding:5px 8px; font-size:10.5px; vertical-align:middle; color:#222;">{{ $sesi->materi->nama_materi ?? '-' }}</td>
                                        <td style="border-bottom:1px solid #e0e0e0; border-left:1px solid #e8e8e8; border-right:1px solid #e8e8e8; border-top:none; padding:5px 8px; font-size:10.5px; vertical-align:middle; color:#222; text-align:center;">{{ $menit > 0 ? $menit . ' Menit' : '—' }}</td>
                                        <td style="border-bottom:1px solid #e0e0e0; border-left:1px solid #e8e8e8; border-right:1px solid #e8e8e8; border-top:none; padding:5px 8px; font-size:10.5px; vertical-align:middle; color:#222;">{{ $sesi->pengajar ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align:center; padding:8px; color:#999; font-size:10px; border:1px solid #e8e8e8;">Tidak ada data materi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="border:1px solid #2d6a2d; background-color:#e0ede0; padding:5px 8px; font-size:10.5px; font-weight:bold; color:#1a4a1a; text-align:right;">TOTAL DURASI</td>
                                    <td style="border:1px solid #2d6a2d; background-color:#e0ede0; padding:5px 8px; font-size:10.5px; font-weight:bold; color:#1a4a1a; text-align:center;">{{ $totalMenit > 0 ? $totalMenit . ' Menit' : '—' }}</td>
                                    <td style="border:1px solid #2d6a2d; background-color:#e0ede0; padding:5px 8px;"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <table style="width:100%; border-collapse:collapse; table-layout:fixed; padding-top:10px;">
                            <tbody>
                                <tr>
                                    <td style="width:58%; padding-right:8px; vertical-align:top; padding-top:10px;">
                                        @if ($sertDetail)
                                        <div style="font-size:10px; color:#333; line-height:1.75;">
                                            <div style="font-weight:bold; font-size:10px; color:#1a4a1a; line-height:1.75;">
                                                DEWAN INSTRUKTUR<br>
                                                {{ strtoupper($sertPelatihan?->nama_pelatihan ?? 'PELATIHAN') }}<br>
                                                PIMPINAN CABANG GP ANSOR KABUPATEN KUDUS
                                            </div>
                                            <div style="margin-top:5px; font-size:10px; color:#555;">Instruktur Cabang,</div>
                                            <span style="height:28px; display:block;"></span>
                                            <span style="font-weight:bold; border-top:1.5px solid #2d6a2d; padding-top:3px; display:inline-block; min-width:150px; font-size:10px; color:#1a4a1a;">{{ $sertTemplate?->nama_instruktur ?? $sertDetail->pengajar ?? '...' }}</span>
                                            <div style="font-size:9px; color:#666; margin-top:2px; text-transform:uppercase; letter-spacing:0.5px;">Instruktur</div>
                                        </div>
                                        @endif
                                    </td>
                                    <td style="width:42%; text-align:center; vertical-align:top; padding-top:10px;">
                                        @php
                                            $nia   = $anggota->nia ?? $anggota->nomor_registrasi ?? '';
                                            $qrUrl = url('/verifikasi/sertifikat/' . urlencode($nia));
                                        @endphp
                                        @if (!empty($nia))
                                        <div style="text-align:center;">
                                            <div style="display:inline-block; border:1.5px solid #2d6a2d; border-radius:4px; padding:4px; background:#fff;">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrUrl) }}"
                                                     alt="QR Verifikasi"
                                                     style="width:68px; height:68px; display:block; margin:0 auto;"
                                                     onerror="this.style.display='none'" />
                                            </div>
                                            <div style="font-size:8px; color:#555; line-height:1.6; margin-top:4px; text-align:center;">
                                                Scan untuk verifikasi<br>
                                                <strong style="color:#2d6a2d; font-size:8px;">NIA: {{ $nia }}</strong>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            {{-- INFO STRIP --}}
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0; border-top:1.5px solid #ccfbf1;">
                @php $apRecord = $this->getAnggotaPelatihanForPrintLog($latestPrintLog); @endphp
                @foreach ([
                    ['label' => 'Pelatihan',     'value' => $sertPelatihan?->nama_pelatihan ?? '—',                                                       'icon' => 'heroicon-o-academic-cap'],
                    ['label' => 'Tanggal Cetak', 'value' => ($latestPrintLog->tanggal_cetak ?? $latestPrintLog->created_at)?->format('d/m/Y H:i') ?? '—', 'icon' => 'heroicon-o-calendar-days'],
                    ['label' => 'No. Sertifikat','value' => $apRecord?->sertifikat_nomor ?? '—',                                                           'icon' => 'heroicon-o-document-text'],
                    ['label' => 'Total Cetak',   'value' => $sertPrintLogs->count() . ' sertifikat',                                                       'icon' => 'heroicon-o-printer'],
                ] as $i => $info)
                    <div style="padding:0.875rem 1.25rem; {{ $i > 0 ? 'border-left:1.5px solid #ccfbf1;' : '' }}">
                        <div style="display:flex; align-items:center; gap:0.375rem; margin-bottom:0.25rem;">
                            @svg($info['icon'], 'w-3 h-3', ['style' => 'color:#0d9488;width:0.75rem;height:0.75rem;flex-shrink:0;'])
                            <p style="font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af;">{{ $info['label'] }}</p>
                        </div>
                        <p style="font-size:0.8125rem; font-weight:800; color:#1f2937; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $info['value'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Riwayat Log Cetak --}}
            @if ($sertPrintLogs->count() > 0)
                <div style="border-top:1.5px solid #ccfbf1; background:#f0fdfa;">
                    <div style="padding:0.75rem 1.25rem; border-bottom:1px solid #ccfbf1;">
                        <p style="font-size:0.75rem; font-weight:700; color:#0d9488; text-transform:uppercase; letter-spacing:0.06em;">Riwayat Log Cetak Sertifikat</p>
                    </div>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                            <thead>
                                <tr style="background:#ccfbf1; border-bottom:1px solid #99f6e4;">
                                    @foreach (['Pelatihan / Sesi', 'Template', 'Tanggal Cetak', 'Dicetak Oleh', 'No. Sertifikat', ''] as $th)
                                        <th style="padding:0.625rem 1.25rem; text-align:left; font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#0d9488; white-space:nowrap;">{{ $th }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sertPrintLogs as $pl)
                                    @php $plAp = $this->getAnggotaPelatihanForPrintLog($pl); @endphp
                                    <tr style="border-bottom:1px solid #f0fdfa; {{ $pl->id === $latestPrintLog->id ? 'background:#f0fdfa;' : '' }}"
                                        onmouseover="this.style.background='#f0fdfa'"
                                        onmouseout="this.style.background='{{ $pl->id === $latestPrintLog->id ? '#f0fdfa' : '' }}'">
                                        <td style="padding:0.75rem 1.25rem;">
                                            @if ($pl->id === $latestPrintLog->id)
                                                <span style="display:inline-flex; align-items:center; border-radius:9999px; padding:0.1rem 0.5rem; font-size:0.6rem; font-weight:700; background:#ccfbf1; color:#0d9488; border:1px solid #5eead4; white-space:nowrap; margin-bottom:2px;">Preview</span><br>
                                            @endif
                                            <p style="font-weight:600; color:#1f2937; line-height:1.3;">{{ $pl->pelatihanDetail?->pelatihan?->nama_pelatihan ?? '—' }}</p>
                                            @if ($pl->pelatihanDetail?->materi?->nama_materi)
                                                <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.125rem;">
                                                    {{ $pl->pelatihanDetail->materi->nama_materi }}
                                                    @if ($pl->pelatihanDetail->tanggal)
                                                        · {{ \Carbon\Carbon::parse($pl->pelatihanDetail->tanggal)->format('d/m/Y') }}
                                                    @endif
                                                </p>
                                            @endif
                                        </td>
                                        <td style="padding:0.75rem 1.25rem; font-size:0.8125rem; color:#374151;">{{ $pl->templateSertifikat?->nama_template ?? $pl->templateSertifikat?->nama ?? '—' }}</td>
                                        <td style="padding:0.75rem 1.25rem; font-size:0.8125rem; color:#374151; white-space:nowrap;">{{ ($pl->tanggal_cetak ?? $pl->created_at)?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td style="padding:0.75rem 1.25rem; font-size:0.8125rem; color:#374151;">{{ $pl->pencetak?->name ?? '—' }}</td>
                                        <td style="padding:0.75rem 1.25rem;">
                                            @if ($plAp?->sertifikat_nomor)
                                                <span style="display:inline-flex; align-items:center; border-radius:0.375rem; padding:0.2rem 0.625rem; font-size:0.75rem; font-weight:700; background:#ccfbf1; color:#065f46; font-family:monospace;">{{ $plAp->sertifikat_nomor }}</span>
                                            @else
                                                <span style="color:#d1d5db; font-size:0.75rem;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding:0.75rem 1.25rem; text-align:right;">
                                            <a href="{{ route('print-logs.pdf', $pl->id) }}"
                                               target="_blank"
                                               style="display:inline-flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem 0.625rem; font-size:0.75rem; font-weight:600; color:#0d9488; background:#f0fdfa; border:1px solid #99f6e4; text-decoration:none; gap:0.25rem; white-space:nowrap;"
                                               onmouseover="this.style.background='#ccfbf1';this.style.color='#0f766e'"
                                               onmouseout="this.style.background='#f0fdfa';this.style.color='#0d9488'">
                                                <x-heroicon-o-arrow-down-tray style="width:0.75rem; height:0.75rem;" />
                                                PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════
         TABEL RIWAYAT PELATIHAN  (tidak diubah)
    ══════════════════════════════════════════════════════ --}}
    <div style="border-radius:1.25rem; background:#fff; overflow:hidden; border:1.5px solid #a7f3d0; box-shadow:0 1px 6px rgba(5,150,105,0.06);">

        <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:linear-gradient(to right,#f0fdf4,#ecfdf5); border-bottom:1.5px solid #a7f3d0;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; background:#059669; flex-shrink:0;">
                    <x-heroicon-s-academic-cap style="width:1rem; height:1rem; color:white;" />
                </div>
                <div>
                    <p style="font-size:0.875rem; font-weight:700; color:#1f2937;">Riwayat Pelatihan</p>
                    <p style="font-size:0.75rem; font-weight:600; color:#059669;">{{ $anggota->pelatihanRecords->count() }} sesi tercatat</p>
                </div>
            </div>
            <button wire:click="openCreateModal"
                    style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.75rem; padding:0.5rem 1rem; font-size:0.75rem; font-weight:700; color:#fff; background:linear-gradient(135deg,#059669,#34d399); box-shadow:0 2px 8px rgba(5,150,105,0.28); border:none; cursor:pointer; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <x-heroicon-o-plus style="width:0.875rem; height:0.875rem;" />
                Tambah Kehadiran
            </button>
        </div>

        @if ($anggota->pelatihanRecords->isEmpty())
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.625rem; padding:5rem 2rem;">
                <div style="display:flex; align-items:center; justify-content:center; width:3.5rem; height:3.5rem; border-radius:1rem; background:#d1fae5;">
                    <x-heroicon-o-academic-cap style="width:1.75rem; height:1.75rem; color:#059669;" />
                </div>
                <p style="font-size:0.875rem; font-weight:600; color:#6b7280;">Belum ada data pelatihan</p>
                <p style="font-size:0.75rem; color:#9ca3af;">Klik "Tambah Kehadiran" untuk memulai</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                    <thead>
                        <tr style="background:#f0fdf4; border-bottom:1.5px solid #a7f3d0;">
                            @foreach (['Pelatihan', 'Materi / Tempat', 'Tanggal', 'Kehadiran', 'Skor', 'Sertifikat', ''] as $th)
                                <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.625rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#059669; white-space:nowrap;">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($anggota->pelatihanRecords->sortByDesc(fn($r) => $r->pelatihanDetail?->tanggal) as $ap)
                            @php
                                [$sBg, $sText, $sBorder] = match($ap->status_kehadiran) {
                                    'Hadir'       => ['#d1fae5', '#065f46', '#6ee7b7'],
                                    'Tidak Hadir' => ['#fee2e2', '#9f1239', '#fca5a5'],
                                    'Izin'        => ['#fef9c3', '#854d0e', '#fcd34d'],
                                    'Sakit'       => ['#ede9fe', '#4c1d95', '#c4b5fd'],
                                    default       => ['#f3f4f6', '#374151', '#d1d5db'],
                                };
                            @endphp
                            <tr style="border-bottom:1px solid #ecfdf5;"
                                onmouseover="this.style.background='#f0fdf4'"
                                onmouseout="this.style.background=''">
                                <td style="padding:1rem 1.25rem;">
                                    <p style="font-weight:600; color:#111827; line-height:1.3;">{{ $ap->pelatihanDetail?->pelatihan?->nama_pelatihan ?? '—' }}</p>
                                    @if ($ap->pelatihanDetail?->pengajar)
                                        <p style="display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#9ca3af; margin-top:0.25rem;">
                                            <x-heroicon-o-user style="width:0.75rem; height:0.75rem; flex-shrink:0;" />
                                            {{ $ap->pelatihanDetail->pengajar }}
                                        </p>
                                    @endif
                                </td>
                                <td style="padding:1rem 1.25rem;">
                                    <p style="font-weight:500; color:#374151;">{{ $ap->pelatihanDetail?->materi?->nama_materi ?? '—' }}</p>
                                    @if ($ap->pelatihanDetail?->tempat)
                                        <p style="display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#9ca3af; margin-top:0.25rem;">
                                            <x-heroicon-o-map-pin style="width:0.75rem; height:0.75rem; flex-shrink:0;" />
                                            {{ $ap->pelatihanDetail->tempat }}
                                        </p>
                                    @endif
                                </td>
                                <td style="padding:1rem 1.25rem; white-space:nowrap;">
                                    @if ($ap->pelatihanDetail?->tanggal)
                                        <p style="font-weight:500; color:#374151;">{{ \Carbon\Carbon::parse($ap->pelatihanDetail->tanggal)->translatedFormat('d M Y') }}</p>
                                        @if ($ap->pelatihanDetail->jam_mulai)
                                            <p style="display:flex; align-items:center; gap:0.25rem; font-size:0.75rem; color:#9ca3af; margin-top:0.25rem;">
                                                <x-heroicon-o-clock style="width:0.75rem; height:0.75rem; flex-shrink:0;" />
                                                {{ \Carbon\Carbon::parse($ap->pelatihanDetail->jam_mulai)->format('H:i') }}{{ $ap->pelatihanDetail->jam_selesai ? ' – ' . \Carbon\Carbon::parse($ap->pelatihanDetail->jam_selesai)->format('H:i') : '' }}
                                            </p>
                                        @endif
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td style="padding:1rem 1.25rem; text-align:center;">
                                    <span style="display:inline-flex; align-items:center; border-radius:9999px; padding:0.25rem 0.625rem; font-size:0.75rem; font-weight:700; white-space:nowrap; background:{{ $sBg }}; color:{{ $sText }}; border:1.5px solid {{ $sBorder }};">
                                        {{ $ap->status_kehadiran }}
                                    </span>
                                </td>
                                <td style="padding:1rem 1.25rem; text-align:center;">
                                    @if ($ap->skor !== null)
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:2.75rem; height:1.75rem; border-radius:0.5rem; font-size:0.75rem; font-weight:900; background:#d1fae5; color:#065f46;">
                                            {{ number_format($ap->skor, 0) }}
                                        </span>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td style="padding:1rem 1.25rem;">
                                    @if ($ap->sertifikat_nomor)
                                        <div style="display:flex; align-items:flex-start; gap:0.5rem;">
                                            <div style="display:flex; align-items:center; justify-content:center; width:1.25rem; height:1.25rem; border-radius:0.375rem; background:#d1fae5; margin-top:0.0625rem; flex-shrink:0;">
                                                <x-heroicon-o-document-check style="width:0.75rem; height:0.75rem; color:#059669;" />
                                            </div>
                                            <div>
                                                <p style="font-size:0.75rem; font-weight:600; color:#374151;">{{ $ap->sertifikat_nomor }}</p>
                                                @if ($ap->tanggal_terbit_sertifikat)
                                                    <p style="font-size:0.75rem; color:#9ca3af;">{{ \Carbon\Carbon::parse($ap->tanggal_terbit_sertifikat)->translatedFormat('d M Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span style="font-size:0.75rem; color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td style="padding:1rem 1.25rem;">
                                    <div style="display:flex; align-items:center; justify-content:flex-end; gap:0.25rem;">
                                        <button wire:click="openEditModal({{ $ap->id }})"
                                                style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                                onmouseover="this.style.background='#d1fae5';this.style.color='#059669'"
                                                onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                            <x-heroicon-o-pencil-square style="width:1rem; height:1rem;" />
                                        </button>
                                        <button wire:click="delete({{ $ap->id }})"
                                                wire:confirm="Yakin ingin menghapus data ini?"
                                                style="display:flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem; color:#9ca3af; background:transparent; border:none; cursor:pointer;"
                                                onmouseover="this.style.background='#fee2e2';this.style.color='#e11d48'"
                                                onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                                            <x-heroicon-o-trash style="width:1rem; height:1rem;" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════
         MODAL TAMBAH / EDIT KEHADIRAN
    ══════════════════════════════════════════════════════ --}}
    <x-filament::modal id="pelatihan-modal" :heading="$editingId ? 'Edit Kehadiran' : 'Tambah Kehadiran'" width="2xl">
        <form wire:submit="save">
            {{ $this->form }}
            <div style="display:flex; justify-content:flex-end; gap:0.625rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid #d1fae5;">
                <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'pelatihan-modal' })">Batal</x-filament::button>
                <x-filament::button type="submit" color="primary">{{ $editingId ? 'Simpan Perubahan' : 'Tambahkan' }}</x-filament::button>
            </div>
        </form>
    </x-filament::modal>


    {{-- ══════════════════════════════════════════════════════
         MODAL EDIT PROFIL
    ══════════════════════════════════════════════════════ --}}
    <x-filament::modal id="profil-modal" heading="Edit Data Pribadi" width="4xl">
        <form wire:submit="saveProfil">
            {{ $this->editProfilForm }}
            <div style="display:flex; justify-content:flex-end; gap:0.625rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid #d1fae5;">
                <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'profil-modal' })">Batal</x-filament::button>
                <x-filament::button type="submit" color="primary">Simpan Perubahan</x-filament::button>
            </div>
        </form>
    </x-filament::modal>


    {{-- ══════════════════════════════════════════════════════
         MODAL TAMBAH / EDIT PENDIDIKAN
    ══════════════════════════════════════════════════════ --}}
    <x-filament::modal id="pendidikan-modal" :heading="$editingPendidikanId ? 'Edit Pendidikan' : 'Tambah Pendidikan'" width="2xl">
        <form wire:submit="savePendidikan">
            {{ $this->pendidikanForm }}
            <div style="display:flex; justify-content:flex-end; gap:0.625rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid #dbeafe;">
                <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'pendidikan-modal' })">Batal</x-filament::button>
                <x-filament::button type="submit" color="primary">{{ $editingPendidikanId ? 'Simpan Perubahan' : 'Tambahkan' }}</x-filament::button>
            </div>
        </form>
    </x-filament::modal>


    {{-- ══════════════════════════════════════════════════════
         MODAL TAMBAH / EDIT STRUKTUR ORGANISASI
    ══════════════════════════════════════════════════════ --}}
    <x-filament::modal id="struktur-modal" :heading="$editingStrukturId ? 'Edit Jabatan' : 'Tambah Jabatan'" width="2xl">
        <form wire:submit="saveStruktur">
            {{ $this->strukturForm }}
            <div style="display:flex; justify-content:flex-end; gap:0.625rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid #d1fae5;">
                <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'struktur-modal' })">Batal</x-filament::button>
                <x-filament::button type="submit" color="primary">{{ $editingStrukturId ? 'Simpan Perubahan' : 'Tambahkan' }}</x-filament::button>
            </div>
        </form>
    </x-filament::modal>


    {{-- ══════════════════════════════════════════════════════
         MODAL TAMBAH / EDIT SOCIAL MEDIA
    ══════════════════════════════════════════════════════ --}}
    <x-filament::modal id="sosmed-modal" :heading="$editingSosmedId ? 'Edit Social Media' : 'Tambah Social Media'" width="xl">
        <form wire:submit="saveSosmed">
            {{ $this->sosmedForm }}
            <div style="display:flex; justify-content:flex-end; gap:0.625rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid #e9d5ff;">
                <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'sosmed-modal' })">Batal</x-filament::button>
                <x-filament::button type="submit" color="primary">{{ $editingSosmedId ? 'Simpan Perubahan' : 'Tambahkan' }}</x-filament::button>
            </div>
        </form>
    </x-filament::modal>

</x-filament-panels::page>