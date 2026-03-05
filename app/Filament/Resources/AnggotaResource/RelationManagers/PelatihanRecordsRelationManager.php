<?php

namespace App\Filament\Resources\AnggotaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PelatihanRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'pelatihanRecords';

    protected static ?string $title = 'Riwayat Pelatihan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pelatihan_detail_id')
                    ->relationship('pelatihanDetail', 'id')
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "{$record->pelatihan->nama_pelatihan} - {$record->materi->nama_materi} ({$record->tanggal})"
                    ),
                Forms\Components\Select::make('status_kehadiran')
                    ->required()
                    ->options([
                        'Hadir' => 'Hadir',
                        'Tidak Hadir' => 'Tidak Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                    ])
                    ->default('Hadir'),
                Forms\Components\TextInput::make('skor')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Textarea::make('keterangan')
                    ->rows(3),
                Forms\Components\TextInput::make('sertifikat_nomor')
                    ->maxLength(100),
                Forms\Components\FileUpload::make('sertifikat_path')
                    ->directory('sertifikat'),
                Forms\Components\DatePicker::make('tanggal_terbit_sertifikat'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelatihanDetail.pelatihan.nama_pelatihan')
                    ->label('Pelatihan'),
                Tables\Columns\TextColumn::make('pelatihanDetail.materi.nama_materi')
                    ->label('Materi'),
                Tables\Columns\TextColumn::make('pelatihanDetail.tanggal')
                    ->label('Tanggal')
                    ->date(),
                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Tidak Hadir' => 'danger',
                        'Izin' => 'warning',
                        'Sakit' => 'info',
                    }),
                Tables\Columns\TextColumn::make('skor'),
                Tables\Columns\TextColumn::make('sertifikat_nomor'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
