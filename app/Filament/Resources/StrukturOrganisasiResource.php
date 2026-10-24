<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StrukturOrganisasiResource\Pages;
use App\Models\StrukturOrganisasi;
use App\Models\Level;
use App\Models\Jabatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class StrukturOrganisasiResource extends Resource
{
    protected static ?string $model = StrukturOrganisasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Struktur Organisasi';

    protected static ?string $modelLabel = 'Struktur Organisasi';

    protected static ?string $pluralModelLabel = 'Struktur Organisasi';

    protected static ?string $navigationGroup = 'Organisasi';
    protected static ?string $slug = 'struktur-organisasi';
    protected static bool $shouldRegisterNavigation = false;

    // =========================================================
    // FORM
    // =========================================================
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informasi Anggota')
                ->schema([
                    Forms\Components\Select::make('anggota_id')
                        ->label('Anggota')
                        ->relationship('anggota', 'nama_lengkap') // ← sesuaikan kolom nama di tabel anggotas
                        ->searchable()
                        ->preload()
                        ->required(),

                    // Level tidak bisa pakai ->relationship() langsung karena labelnya
                    // berasal dari accessor polymorphic (nama_level).
                    // Kita gunakan Select dengan options yang di-generate manual.
                    Forms\Components\Select::make('level_id')
                        ->label('Level')
                        ->options(function () {
                            return Level::all()->mapWithKeys(function ($level) {
                                $namaLevel = $level->nama_level ?? "{$level->level_type} #{$level->id}";
                                return [$level->id => "[{$level->level_type}] {$namaLevel}"];
                            });
                        })
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('jabatan_id')
                        ->label('Jabatan')
                        ->relationship('jabatan', 'nama_jabatan')
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->columns(3),

            Forms\Components\Section::make('Masa Khidmat')
                ->schema([
                    Forms\Components\DatePicker::make('masa_khidmat_mulai')
                        ->label('Mulai')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    Forms\Components\DatePicker::make('masa_khidmat_selesai')
                        ->label('Selesai')
                        ->nullable()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->after('masa_khidmat_mulai'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger')
                        ->inline(false),
                ])
                ->columns(3),

            Forms\Components\Section::make('Surat Keputusan')
                ->schema([
                    Forms\Components\TextInput::make('sk_nomor')
                        ->label('Nomor SK')
                        ->maxLength(100)
                        ->placeholder('Contoh: SK/001/ORG/2024'),

                    Forms\Components\DatePicker::make('sk_tanggal')
                        ->label('Tanggal SK')
                        ->nullable()
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                ])
                ->columns(2),

        ]);
    }

    // =========================================================
    // TABLE
    // =========================================================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anggota.nama_lengkap') // ← sesuaikan kolom nama
                    ->label('Anggota')
                    ->searchable()
                    ->sortable(),

                // Level: tampilkan level_type dari relasi level
                Tables\Columns\TextColumn::make('level.level_type')
                    ->label('Level Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PC'      => 'info',
                        'PAC'     => 'warning',
                        'RANTING' => 'success',
                        default   => 'gray',
                    })
                    ->sortable(),

                // Nama level dari accessor (tidak bisa sortable langsung)
                Tables\Columns\TextColumn::make('level.nama_level')
                    ->label('Nama Level')
                    ->getStateUsing(fn($record) => $record->level?->nama_level ?? '—')
                    ->searchable(false),

                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('masa_khidmat_mulai')
                    ->label('Mulai')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('masa_khidmat_selesai')
                    ->label('Selesai')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('sk_nomor')
                    ->label('Nomor SK')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sk_tanggal')
                    ->label('Tanggal SK')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level_id')
                    ->label('Level')
                    ->options(function () {
                        return Level::all()->mapWithKeys(function ($level) {
                            $namaLevel = $level->nama_level ?? "{$level->level_type} #{$level->id}";
                            return [$level->id => "[{$level->level_type}] {$namaLevel}"];
                        });
                    }),

                Tables\Filters\SelectFilter::make('level_type')
                    ->label('Tipe Level')
                    ->relationship('level', 'level_type')
                    ->options([
                        'PC'      => 'PC',
                        'PAC'     => 'PAC',
                        'RANTING' => 'Ranting',
                    ])
                    ->query(function ($query, array $data) {
                        if (filled($data['value'])) {
                            $query->whereHas('level', fn($q) => $q->where('level_type', $data['value']));
                        }
                    }),

                Tables\Filters\SelectFilter::make('jabatan_id')
                    ->label('Jabatan')
                    ->relationship('jabatan', 'nama_jabatan')
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // =========================================================
    // INFOLIST (View Page)
    // =========================================================
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Informasi Anggota')
                ->schema([
                    Infolists\Components\TextEntry::make('anggota.nama') // ← sesuaikan kolom nama
                        ->label('Anggota'),

                    Infolists\Components\TextEntry::make('level.level_type')
                        ->label('Tipe Level')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'PC'      => 'info',
                            'PAC'     => 'warning',
                            'RANTING' => 'success',
                            default   => 'gray',
                        }),

                    Infolists\Components\TextEntry::make('level.nama_level')
                        ->label('Nama Level')
                        ->getStateUsing(fn($record) => $record->level?->nama_level ?? '—'),

                    Infolists\Components\TextEntry::make('jabatan.nama_jabatan')
                        ->label('Jabatan'),
                ])
                ->columns(2),

            Infolists\Components\Section::make('Masa Khidmat & Status')
                ->schema([
                    Infolists\Components\TextEntry::make('masa_khidmat_mulai')
                        ->label('Mulai')
                        ->date('d/m/Y'),

                    Infolists\Components\TextEntry::make('masa_khidmat_selesai')
                        ->label('Selesai')
                        ->date('d/m/Y')
                        ->placeholder('—'),

                    Infolists\Components\IconEntry::make('is_active')
                        ->label('Status Aktif')
                        ->boolean()
                        ->trueIcon('heroicon-o-check-circle')
                        ->falseIcon('heroicon-o-x-circle')
                        ->trueColor('success')
                        ->falseColor('danger'),
                ])
                ->columns(3),

            Infolists\Components\Section::make('Surat Keputusan')
                ->schema([
                    Infolists\Components\TextEntry::make('sk_nomor')
                        ->label('Nomor SK')
                        ->placeholder('—'),

                    Infolists\Components\TextEntry::make('sk_tanggal')
                        ->label('Tanggal SK')
                        ->date('d/m/Y')
                        ->placeholder('—'),
                ])
                ->columns(2),

            Infolists\Components\Section::make('Timestamp')
                ->schema([
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Dibuat Pada')
                        ->dateTime('d/m/Y H:i'),

                    Infolists\Components\TextEntry::make('updated_at')
                        ->label('Diperbarui Pada')
                        ->dateTime('d/m/Y H:i'),
                ])
                ->columns(2)
                ->collapsed(),

        ]);
    }

    // =========================================================
    // PAGES
    // =========================================================
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStrukturOrganisasis::route('/'),
            'create' => Pages\CreateStrukturOrganisasi::route('/create'),
            // 'view'   => Pages\ViewStrukturOrganisasi::route('/{record}'),
            'edit'   => Pages\EditStrukturOrganisasi::route('/{record}/edit'),
        ];
    }
}
