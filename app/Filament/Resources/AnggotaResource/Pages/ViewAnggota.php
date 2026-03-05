<?php

namespace App\Filament\Resources\AnggotaResource\Pages;

use App\Filament\Resources\AnggotaResource;
use App\Models\Anggota;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewAnggota extends ViewRecord
{
    protected static string $resource = AnggotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('verifikasi')
                ->label('Verifikasi')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn(): bool => $this->record->status_verifikasi === 'Pending')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status_verifikasi'  => 'Diverifikasi',
                        'tanggal_verifikasi' => now(),
                    ]);
                    $this->refreshFormData(['status_verifikasi', 'tanggal_verifikasi']);
                }),

            Actions\Action::make('tolak')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(): bool => $this->record->status_verifikasi === 'Pending')
                ->form([
                    Forms\Components\Textarea::make('catatan_verifikasi')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status_verifikasi'  => 'Ditolak',
                        'tanggal_verifikasi' => now(),
                        'catatan_verifikasi' => $data['catatan_verifikasi'],
                    ]);
                    $this->refreshFormData(['status_verifikasi', 'tanggal_verifikasi', 'catatan_verifikasi']);
                }),

            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            // ----- STATUS VERIFIKASI (highlight di atas) ----------------
            Infolists\Components\Section::make()
                ->schema([
                    Infolists\Components\TextEntry::make('status_verifikasi')
                        ->label('Status Verifikasi')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Pending'      => 'warning',
                            'Diverifikasi' => 'success',
                            'Ditolak'      => 'danger',
                            default        => 'gray',
                        }),

                    Infolists\Components\TextEntry::make('tanggal_verifikasi')
                        ->label('Tanggal Verifikasi')
                        ->dateTime('d M Y, H:i')
                        ->placeholder('-'),

                    Infolists\Components\TextEntry::make('catatan_verifikasi')
                        ->label('Catatan Verifikasi')
                        ->placeholder('-')
                        ->columnSpanFull()
                        ->visible(fn(Anggota $record): bool => ! blank($record->catatan_verifikasi)),
                ])
                ->columns(2),

            // ----- NIA --------------------------------------------------
            Infolists\Components\Section::make('Nomor Induk Anggota (NIA)')
                ->icon('heroicon-o-key')
                ->schema([
                    Infolists\Components\TextEntry::make('nia')
                        ->label('NIA')
                        ->fontFamily('mono')
                        ->copyable()
                        ->copyMessage('NIA disalin!')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),

            // ----- FOTO & DATA PRIBADI ----------------------------------
            Infolists\Components\Section::make('Data Pribadi')
                ->icon('heroicon-o-identification')
                ->schema([
                    Infolists\Components\ImageEntry::make('foto')
                        ->label('Foto')
                        ->circular()
                        ->defaultImageUrl(asset('images/default-avatar.png'))
                        ->height(120)
                        ->columnSpan(1),

                    Infolists\Components\Group::make([
                        Infolists\Components\TextEntry::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->weight(FontWeight::Bold)
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                        Infolists\Components\TextEntry::make('nik')
                            ->label('NIK')
                            ->copyable()
                            ->fontFamily('mono'),

                        Infolists\Components\TextEntry::make('umur')
                            ->label('Umur')
                            ->getStateUsing(
                                fn(Anggota $record): string => $record->umur
                                    ? $record->umur . ' tahun'
                                    : '-'
                            ),
                    ])->columnSpan(3),

                    Infolists\Components\TextEntry::make('tempat_lahir')
                        ->label('Tempat Lahir'),

                    Infolists\Components\TextEntry::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->date('d M Y'),

                    Infolists\Components\TextEntry::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                        ->badge()
                        ->color(fn(string $state): string => $state === 'L' ? 'info' : 'pink'),

                    Infolists\Components\TextEntry::make('status_pernikahan')
                        ->label('Status Pernikahan'),

                    Infolists\Components\TextEntry::make('golongan_darah')
                        ->label('Golongan Darah')
                        ->badge()
                        ->color('gray'),

                    Infolists\Components\TextEntry::make('tinggi_badan')
                        ->label('Tinggi Badan')
                        ->suffix(' cm'),

                    Infolists\Components\TextEntry::make('berat_badan')
                        ->label('Berat Badan')
                        ->suffix(' kg'),
                ])
                ->columns(4),

            // ----- ALAMAT -----------------------------------------------
            Infolists\Components\Section::make('Alamat')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Infolists\Components\TextEntry::make('kecamatan.nama_kecamatan')
                        ->label('Kecamatan'),

                    Infolists\Components\TextEntry::make('desa.nama_desa')
                        ->label('Desa / Kelurahan'),

                    Infolists\Components\TextEntry::make('rt')
                        ->label('RT'),

                    Infolists\Components\TextEntry::make('rw')
                        ->label('RW'),

                    Infolists\Components\TextEntry::make('alamat_lengkap')
                        ->label('Alamat Lengkap')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ])
                ->columns(4),

            // ----- KONTAK & PEKERJAAN -----------------------------------
            Infolists\Components\Section::make('Kontak & Pekerjaan')
                ->icon('heroicon-o-phone')
                ->schema([
                    Infolists\Components\TextEntry::make('nomor_hp')
                        ->label('Nomor HP')
                        ->copyable(),

                    Infolists\Components\TextEntry::make('alamat_email')
                        ->label('Email')
                        ->copyable()
                        ->placeholder('-'),

                    Infolists\Components\TextEntry::make('pekerjaan.nama_pekerjaan')
                        ->label('Pekerjaan')
                        ->placeholder('-'),

                    // DIPERBAIKI: partai_politik (bukan nama_politik)
                    Infolists\Components\TextEntry::make('politik.partai_politik')
                        ->label('Afiliasi Politik')
                        ->placeholder('-'),
                ])
                ->columns(2),

            // ----- NPWP & BPJS ------------------------------------------
            Infolists\Components\Section::make('NPWP & BPJS')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Infolists\Components\IconEntry::make('npwp_status')
                        ->label('NPWP')
                        ->boolean(),

                    Infolists\Components\TextEntry::make('npwp_nomor')
                        ->label('Nomor NPWP')
                        ->placeholder('-')
                        ->fontFamily('mono'),

                    Infolists\Components\IconEntry::make('bpjs_status')
                        ->label('BPJS')
                        ->boolean(),

                    Infolists\Components\TextEntry::make('bpjs_nomor')
                        ->label('Nomor BPJS')
                        ->placeholder('-')
                        ->fontFamily('mono'),
                ])
                ->columns(2),

            // ----- STRUKTUR ORGANISASI ----------------------------------
            Infolists\Components\Section::make('Struktur Organisasi')
                ->icon('heroicon-o-building-office-2')
                ->description('Riwayat jabatan dan posisi anggota dalam organisasi.')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('strukturOrganisasi')
                        ->label('')
                        ->schema([
                            // Tipe Badge
                            Infolists\Components\TextEntry::make('tipe_organisasi')
                                ->label('Tipe')
                                ->badge()
                                ->formatStateUsing(fn(?string $state): string => match ($state) {
                                    'internal' => 'Internal',
                                    'eksternal' => 'Eksternal',
                                    default => '—',
                                })
                                ->color(fn(?string $state): string => match ($state) {
                                    'internal' => 'info',
                                    'eksternal' => 'warning',
                                    default => 'gray',
                                }),

                            // Jabatan (selalu tampil)
                            Infolists\Components\TextEntry::make('jabatan.nama_jabatan')
                                ->label('Jabatan')
                                ->placeholder('—'),

                            // Status Aktif
                            Infolists\Components\IconEntry::make('is_active')
                                ->label('Aktif')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            // Masa Khidmat
                            Infolists\Components\TextEntry::make('masa_khidmat_mulai')
                                ->label('Mulai')
                                ->date('d/m/Y'),

                            Infolists\Components\TextEntry::make('masa_khidmat_selesai')
                                ->label('Selesai')
                                ->date('d/m/Y')
                                ->placeholder('Sekarang'),

                            // ---- INTERNAL ONLY ----
                            Infolists\Components\TextEntry::make('level.level_type')
                                ->label('Tipe Level')
                                ->badge()
                                ->color(fn(?string $state): string => match ($state) {
                                    'PC'      => 'info',
                                    'PAC'     => 'warning',
                                    'RANTING' => 'success',
                                    default   => 'gray',
                                })
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'internal'),

                            Infolists\Components\TextEntry::make('level.nama_level')
                                ->label('Nama Level')
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'internal'),

                            // ---- EKSTERNAL ONLY ----
                            Infolists\Components\TextEntry::make('organisasi.nama_organisasi')
                                ->label('Organisasi')
                                ->badge()
                                ->color('success')
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'eksternal'
                                    && filled($record->organisasi_id)),

                            Infolists\Components\TextEntry::make('nama_organisasi')
                                ->label('Organisasi (Manual)')
                                ->badge()
                                ->color('gray')
                                ->placeholder('—')
                                ->visible(fn($record): bool => $record->tipe_organisasi === 'eksternal'
                                    && blank($record->organisasi_id)),
                        ])
                        ->columns(4)
                        ->placeholder('Belum ada riwayat struktur organisasi.'),
                ]),
            // ----- SOCIAL MEDIA-----------------------------------
            Infolists\Components\Section::make('Social Media')
                ->icon('heroicon-o-computer-desktop')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('socialMediaAccounts')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('socialMedia.platform_name')
                                ->label('Platform')
                                ->badge()
                                ->color('info'),

                            Infolists\Components\TextEntry::make('username')
                                ->label('Username')
                                ->copyable()
                                ->copyMessage('Username disalin!')
                                ->placeholder('—'),

                            Infolists\Components\TextEntry::make('url')
                                ->label('URL Profil')
                                ->copyable()
                                ->copyMessage('URL disalin!')
                                ->placeholder('—')
                                ->formatStateUsing(fn(?string $state): string => $state ?? '—')
                                ->url(fn(?string $state): ?string => $state, shouldOpenInNewTab: true),
                        ])
                        ->columns(3)
                        ->placeholder('Belum ada akun social media terdaftar.'),
                ]),

            // ----- DOKUMEN KTP ------------------------------------------
            Infolists\Components\Section::make('Dokumen KTP')
                ->icon('heroicon-o-paper-clip')
                ->schema([
                    Infolists\Components\ImageEntry::make('ktp')
                        ->label('Foto KTP')
                        ->height(200)
                        ->columnSpanFull()
                        ->placeholder('Belum ada foto KTP'),
                ])
                ->collapsible(),

        ]);
    }
}
