<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintLogResource\Pages;
use App\Models\Anggota;
use App\Models\Kta;
use App\Models\Pelatihan;
use App\Models\PelatihanDetail;
use App\Models\PrintLog;
use App\Models\TemplateSertifikat;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrintLogResource extends Resource
{
    protected static ?string $model = PrintLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Log Cetak';
    protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Log Cetak';
    protected static ?string $pluralModelLabel = 'Log Cetak';

    // ------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------

    /** Satu baris per pelatihan: "Nama || Tempat || Tanggal Mulai" */
    public static function pelatihanOptions(): array
    {
        return Pelatihan::query()
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->mapWithKeys(fn (Pelatihan $p) => [
                $p->id => $p->nama_pelatihan
                    . ' || ' . ($p->tempat ?: '-')
                    . ' || ' . ($p->tanggal_mulai ? Carbon::parse($p->tanggal_mulai)->format('d/m/Y') : '-'),
            ])
            ->all();
    }

    /** Anggota yang tercatat mengikuti pelatihan tertentu */
    public static function anggotaPeserta(?int $pelatihanId): array
    {
        if (blank($pelatihanId)) {
            return [];
        }

        return Anggota::query()
            ->whereHas('pelatihanRecords.pelatihanDetail', fn ($q) => $q->where('pelatihan_id', $pelatihanId))
            ->orderBy('nama_lengkap')
            ->get()
            ->mapWithKeys(fn (Anggota $a) => [
                $a->id => $a->nama_lengkap . ($a->nia ? " ({$a->nia})" : ''),
            ])
            ->all();
    }

    /**
     * Cari sesi (pelatihan_detail) untuk log cetak:
     * pakai sesi milik anggota itu, kalau tidak ada pakai sesi pertama pelatihan.
     */
    public static function resolveDetailId(?int $pelatihanId, ?int $anggotaId): ?int
    {
        if (blank($pelatihanId)) {
            return null;
        }

        $detailIds = PelatihanDetail::where('pelatihan_id', $pelatihanId)->orderBy('id')->pluck('id');

        if ($anggotaId) {
            $milikAnggota = \App\Models\AnggotaPelatihan::where('anggota_id', $anggotaId)
                ->whereIn('pelatihan_detail_id', $detailIds)
                ->orderBy('pelatihan_detail_id')
                ->value('pelatihan_detail_id');

            if ($milikAnggota) {
                return (int) $milikAnggota;
            }
        }

        return $detailIds->first();
    }

    // ------------------------------------------------------------
    // Form
    // ------------------------------------------------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Jenis Cetakan')
                    ->schema([
                        Forms\Components\Select::make('jenis_cetakan')
                            ->label('Jenis Cetakan')
                            ->options([
                                'KTA'        => 'KTA (Kartu Tanda Anggota)',
                                'Sertifikat' => 'Sertifikat',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('pelatihan_id', null);
                                $set('anggota_ids', []);
                                $set('anggota_id', null);
                            })
                            ->columnSpanFull(),
                    ]),

                // ───────── SERTIFIKAT ─────────
                Forms\Components\Section::make('Data Sertifikat')
                    ->visible(fn (Get $get) => $get('jenis_cetakan') === 'Sertifikat')
                    ->schema([
                        Forms\Components\Select::make('pelatihan_id')
                            ->label('Pelatihan')
                            ->placeholder('Pilih pelatihan')
                            ->options(fn () => static::pelatihanOptions())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Otomatis centang semua peserta pelatihan
                                $set('anggota_ids', array_map('strval', array_keys(static::anggotaPeserta($state))));
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('template_sertifikat_id')
                            ->label('Template Sertifikat')
                            ->options(fn () => TemplateSertifikat::query()->pluck('nama_batch', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(1),

                        // Hanya saat membuat baru: banyak anggota sekaligus
                        Forms\Components\Placeholder::make('info_peserta')
                            ->label('')
                            ->content(function (Get $get) {
                                if (blank($get('pelatihan_id'))) {
                                    return 'Pilih pelatihan untuk menampilkan peserta.';
                                }
                                $n = count(static::anggotaPeserta((int) $get('pelatihan_id')));
                                return $n > 0
                                    ? "{$n} anggota tercatat mengikuti pelatihan ini."
                                    : 'Belum ada anggota yang tercatat mengikuti pelatihan ini.';
                            })
                            ->visibleOn('create')
                            ->columnSpanFull(),

                        Forms\Components\CheckboxList::make('anggota_ids')
                            ->label('Peserta yang dicetak sertifikatnya')
                            ->options(fn (Get $get) => static::anggotaPeserta((int) $get('pelatihan_id') ?: null))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row')
                            ->required()
                            ->visible(fn (Get $get) => filled($get('pelatihan_id')))
                            ->visibleOn('create')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ───────── KTA ─────────
                Forms\Components\Section::make('Data KTA')
                    ->visible(fn (Get $get) => $get('jenis_cetakan') === 'KTA')
                    ->schema([
                        Forms\Components\Select::make('kta_id')
                            ->label('Batch KTA')
                            ->options(fn () => Kta::query()->pluck('nama_batch', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                // ───────── ANGGOTA (KTA saat create, semua jenis saat edit) ─────────
                Forms\Components\Section::make('Anggota')
                    ->visible(fn (Get $get, string $operation) => $operation === 'edit'
                        || $get('jenis_cetakan') === 'KTA')
                    ->schema([
                        Forms\Components\Select::make('anggota_id')
                            ->label('Anggota')
                            ->options(fn () => Anggota::query()->orderBy('nama_lengkap')->pluck('nama_lengkap', 'id'))
                            ->searchable()
                            ->required(fn (Get $get, string $operation) => $operation === 'edit'
                                || $get('jenis_cetakan') === 'KTA')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    // ------------------------------------------------------------
    // Table
    // ------------------------------------------------------------

    public static function table(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->with([
            'anggota',
            'pelatihanDetail.pelatihan',
            'templateSertifikat',
            'pencetak',
        ]))
        ->columns([
            Tables\Columns\TextColumn::make('jenis_cetakan')
                ->label('Jenis')
                ->badge()
                ->color(fn (string $state) => $state === 'KTA' ? 'success' : 'info'),

            Tables\Columns\TextColumn::make('anggota.nama_lengkap')
                ->label('Anggota')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('pelatihanDetail.pelatihan.nama_pelatihan')
                ->label('Pelatihan')
                ->description(fn (PrintLog $r) => $r->pelatihanDetail?->pelatihan?->tempat)
                ->placeholder('—')
                ->wrap(),

            Tables\Columns\TextColumn::make('templateSertifikat.nama_batch')
                ->label('Template')
                ->placeholder('—'),

            Tables\Columns\TextColumn::make('pencetak.name')
                ->label('Dicetak Oleh')
                ->placeholder('—'),

            Tables\Columns\TextColumn::make('tanggal_cetak')
                ->label('Tanggal Cetak')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ])
        ->defaultSort('tanggal_cetak', 'desc')
        ->filters([
            Tables\Filters\SelectFilter::make('jenis_cetakan')
                ->label('Jenis Cetakan')
                ->options(['KTA' => 'KTA', 'Sertifikat' => 'Sertifikat']),

            Tables\Filters\SelectFilter::make('pelatihan')
                ->label('Pelatihan')
                ->options(fn () => static::pelatihanOptions())
                ->searchable()
                ->query(fn (Builder $query, array $data) => $query->when(
                    $data['value'] ?? null,
                    fn ($q, $v) => $q->whereHas('pelatihanDetail', fn ($d) => $d->where('pelatihan_id', $v))
                )),
        ])
        ->actions([
            Action::make('print_kta')
                ->label('Print KTA')
                ->icon('heroicon-o-identification')
                ->color('success')
                ->visible(fn (PrintLog $record) => $record->jenis_cetakan === 'KTA')
                ->url(fn (PrintLog $record) => url('/kta/pdf/' . $record->id))
                ->openUrlInNewTab(),

            Action::make('print_sertifikat')
                ->label('Print Sertifikat')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->visible(fn (PrintLog $record) => $record->jenis_cetakan === 'Sertifikat')
                ->url(fn (PrintLog $record) => route('print-logs.pdf', $record->id))
                ->openUrlInNewTab(),

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
            'index'  => Pages\ListPrintLogs::route('/'),
            'create' => Pages\CreatePrintLog::route('/create'),
            'edit'   => Pages\EditPrintLog::route('/{record}/edit'),
        ];
    }
}