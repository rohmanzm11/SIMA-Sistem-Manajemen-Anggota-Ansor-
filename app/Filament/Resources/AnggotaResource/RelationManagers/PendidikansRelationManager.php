<?php

namespace App\Filament\Resources\AnggotaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PendidikansRelationManager extends RelationManager
{
    protected static string $relationship = 'pendidikans';

    protected static ?string $title = 'Riwayat Pendidikan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenjang')
                    ->required()
                    ->options([
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'SMA' => 'SMA',
                        'SMK' => 'SMK',
                        'MA' => 'MA',
                        'Pesantren' => 'Pesantren',
                        'Diniyyah' => 'Diniyyah',
                        'D1' => 'D1',
                        'D2' => 'D2',
                        'D3' => 'D3',
                        'D4' => 'D4',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ]),
                Forms\Components\TextInput::make('nama_institusi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jurusan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tahun_masuk')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y')),
                Forms\Components\TextInput::make('tahun_lulus')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y') + 10),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'Sedang Berjalan' => 'Sedang Berjalan',
                        'Lulus' => 'Lulus',
                        'Tidak Lulus' => 'Tidak Lulus',
                        'Drop Out' => 'Drop Out',
                    ])
                    ->default('Lulus'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jenjang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_institusi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jurusan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun_masuk'),
                Tables\Columns\TextColumn::make('tahun_lulus'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sedang Berjalan' => 'info',
                        'Lulus' => 'success',
                        'Tidak Lulus' => 'danger',
                        'Drop Out' => 'warning',
                    }),
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
