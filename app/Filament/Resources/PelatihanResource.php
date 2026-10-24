<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelatihanResource\Pages;
use App\Models\Materi;
use App\Models\Pelatihan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PelatihanResource extends Resource
{
    protected static ?string $model = Pelatihan::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Pelatihan';

    protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';

    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Pelatihan';
    protected static ?string $pluralModelLabel = 'Pelatihan';

    public static function form(Form $form): Form
    {
        return $form->schema(static::formSchema());
    }

    /**
     * Skema form dipisah supaya bisa dipakai ulang
     * (Resource ini dan modal di halaman Dashboard Pelatihan).
     */
    public static function formSchema(): array
    {
        return [
            Forms\Components\Section::make('Informasi Pelatihan')
                ->schema([
                    Forms\Components\TextInput::make('nama_pelatihan')
                        ->label('Nama Pelatihan')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('tempat')
                        ->label('Tempat')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    Forms\Components\DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->afterOrEqual('tanggal_mulai'),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Materi Pelatihan')
                ->description('Materi yang diisi di sini otomatis tersimpan ke tabel materi.')
                ->schema([
                    Forms\Components\Repeater::make('pelatihanDetails')
                        ->label('')
                        ->relationship('pelatihanDetails')
                        ->schema([
                            // Menyimpan id materi supaya saat edit, materi yang sama di-update (bukan dibuat ulang)
                            Forms\Components\Hidden::make('materi_id'),

                            Forms\Components\TextInput::make('nama_materi')
                                ->label('Nama Materi')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('pengajar')
                                ->label('Pengajar')
                                ->maxLength(255)
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('skor_maksimal')
                                ->label('Skor Maksimal')
                                ->numeric()
                                ->default(100)
                                ->minValue(0)
                                ->maxValue(999.99)
                                ->columnSpan(2),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true)
                                ->inline(false)
                                ->columnSpan(2),
                        ])
                        // Saat form edit dibuka: isi nama_materi & skor_maksimal dari tabel materis
                        ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                            $materi = filled($data['materi_id'] ?? null)
                                ? Materi::find($data['materi_id'])
                                : null;

                            $data['nama_materi']   = $materi?->nama_materi;
                            $data['skor_maksimal'] = $materi?->skor_maksimal ?? 100;

                            return $data;
                        })
                        // Saat menambah materi baru: buat baris di tabel materis
                        ->mutateRelationshipDataBeforeCreateUsing(
                            fn(array $data): array => static::simpanMateri($data)
                        )
                        // Saat menyimpan perubahan: update baris materis yang sudah ada
                        ->mutateRelationshipDataBeforeSaveUsing(
                            fn(array $data): array => static::simpanMateri($data)
                        )
                        ->columns(4)
                        ->addActionLabel('+ Tambah Materi')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->collapseAllAction(
                            fn(Forms\Components\Actions\Action $action) => $action->label('Tutup Semua')
                        )
                        ->expandAllAction(
                            fn(Forms\Components\Actions\Action $action) => $action->label('Buka Semua')
                        )
                        ->itemLabel(
                            fn(array $state): ?string => filled($state['nama_materi'] ?? null)
                                ? $state['nama_materi']
                                : 'Materi Baru'
                        )
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * Simpan/update data ke tabel materis, lalu sisakan hanya kolom milik pelatihan_details.
     */
    protected static function simpanMateri(array $data): array
    {
        $atribut = [
            'nama_materi'   => $data['nama_materi'],
            'skor_maksimal' => $data['skor_maksimal'] ?? 100,
        ];

        $materi = filled($data['materi_id'] ?? null)
            ? Materi::find($data['materi_id'])
            : null;

        if ($materi) {
            $materi->update($atribut);
        } else {
            $materi = Materi::create($atribut);
        }

        $data['materi_id'] = $materi->id;

        unset($data['nama_materi'], $data['skor_maksimal']);

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            // Muat relasi sekali saja supaya tidak ada query berulang per baris
            ->modifyQueryUsing(fn(Builder $query) => $query->with('pelatihanDetails'))
            ->columns([
                // Nama pelatihan + tempat digabung dalam satu kolom (tempat tampil di bawah nama)
                Tables\Columns\TextColumn::make('nama_pelatihan')
                    ->label('Pelatihan')
                    ->description(fn($record): string => $record->tempat ?? '—')
                    ->searchable(['nama_pelatihan', 'tempat'])
                    ->sortable()
                    ->wrap()
                    ->weight('medium'),

                // Tanggal mulai & selesai digabung: "05 Okt 2026 – 07 Okt 2026"
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal')
                    ->formatStateUsing(function ($state, $record): string {
                        $mulai   = Carbon::parse($record->tanggal_mulai)->translatedFormat('d M Y');
                        $selesai = $record->tanggal_selesai
                            ? Carbon::parse($record->tanggal_selesai)->translatedFormat('d M Y')
                            : null;

                        return ($selesai && $selesai !== $mulai)
                            ? "{$mulai} – {$selesai}"
                            : $mulai;
                    })
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('pelatihan_details_count')
                    ->counts('pelatihanDetails')
                    ->label('Materi')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                // Pengajar ditumpuk ke bawah (bukan memanjang ke samping) sehingga tidak perlu digeser
                Tables\Columns\TextColumn::make('pengajar_list')
                    ->label('Pengajar')
                    ->getStateUsing(
                        fn($record): array => $record->pelatihanDetails
                            ->pluck('pengajar')
                            ->filter()
                            ->unique()
                            ->values()
                            ->all()
                    )
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->placeholder('—')
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal_mulai', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'edit'   => Pages\EditPelatihan::route('/{record}/edit'),
        ];
    }
}