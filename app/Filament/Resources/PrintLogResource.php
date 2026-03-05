<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintLogResource\Pages;
use App\Models\Anggota;
use App\Models\Kta;
use App\Models\PelatihanDetail;
use App\Models\PrintLog;
use App\Models\TemplateSertifikat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class PrintLogResource extends Resource
{
    protected static ?string $model = PrintLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';

    protected static ?string $navigationLabel = 'Log Cetak';

    protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Log Cetak';

    protected static ?string $pluralModelLabel = 'Log Cetak';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Cetak')
                    ->schema([
                        Forms\Components\Select::make('jenis_cetakan')
                            ->label('Jenis Cetakan')
                            ->options([
                                'KTA'        => 'KTA (Kartu Tanda Anggota)',
                                'Sertifikat' => 'Sertifikat',
                            ])
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        Forms\Components\Select::make('anggota_id')
                            ->label('Anggota')
                            ->options(Anggota::query()->pluck('nama_lengkap', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('pelatihan_detail_id')
                            ->label('Sesi Pelatihan')
                            ->options(function () {
                                return PelatihanDetail::with('pelatihan')
                                    ->get()
                                    ->mapWithKeys(fn($d) => [
                                        $d->id => ($d->pelatihan->nama_pelatihan ?? '-')
                                            . ' — '
                                            . ($d->tanggal
                                                ? \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y')
                                                : '-'),
                                    ]);
                            })
                            ->searchable()
                            ->nullable()
                            ->visible(fn(Forms\Get $get) => $get('jenis_cetakan') === 'Sertifikat')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('template_sertifikat_id')
                            ->label('Template Sertifikat')
                            ->options(TemplateSertifikat::query()->pluck('nama_batch', 'id'))
                            ->searchable()
                            ->nullable()
                            ->visible(fn(Forms\Get $get) => $get('jenis_cetakan') === 'Sertifikat')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('kta_id')
                            ->label('Data KTA')
                            ->options(function () {
                                return Kta::with('anggota')
                                    ->get()
                                    ->mapWithKeys(fn($k) => [
                                        $k->id => ($k->nomor_kta ?? $k->id)
                                            . ' — '
                                            . ($k->anggota->nama_lengkap ?? '-'),
                                    ]);
                            })
                            ->searchable()
                            ->nullable()
                            ->visible(fn(Forms\Get $get) => $get('jenis_cetakan') === 'KTA')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('jenis_cetakan')
                    ->label('Jenis')
                    ->colors([
                        'success' => 'KTA',
                        'info'    => 'Sertifikat',
                    ]),

                Tables\Columns\TextColumn::make('anggota.nama_lengkap')
                    ->label('Anggota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pelatihanDetail.pelatihan.nama_pelatihan')
                    ->label('Pelatihan')
                    ->default('—'),

                Tables\Columns\TextColumn::make('templateSertifikat.nama_batch')
                    ->label('Template')
                    ->default('—'),

                Tables\Columns\TextColumn::make('pencetak.name')
                    ->label('Dicetak Oleh')
                    ->default('—'),

                Tables\Columns\TextColumn::make('tanggal_cetak')
                    ->label('Tanggal Cetak')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('tanggal_cetak', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_cetakan')
                    ->label('Jenis Cetakan')
                    ->options([
                        'KTA'        => 'KTA',
                        'Sertifikat' => 'Sertifikat',
                    ]),
            ])
            ->actions([
                // ── Print KTA → langsung buka tab baru download PDF ──
                Action::make('print_kta')
                    ->label('Print KTA')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->visible(fn(PrintLog $record) => $record->jenis_cetakan === 'KTA')
                    ->url(fn(PrintLog $record) => url('/kta/pdf/' . $record->id))
                    ->openUrlInNewTab(),

                // ── Print Sertifikat ──
                Action::make('print_sertifikat')
                    ->label('Print Sertifikat')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->visible(fn(PrintLog $record) => $record->jenis_cetakan === 'Sertifikat')
                    ->url(fn(PrintLog $record) => route('print-logs.pdf', $record->id))
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
