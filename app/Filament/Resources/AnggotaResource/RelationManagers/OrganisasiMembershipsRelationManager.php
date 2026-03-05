<?php

namespace App\Filament\Resources\AnggotaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrganisasiMembershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'organisasiMemberships';

    protected static ?string $title = 'Keanggotaan Organisasi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organisasi_id')
                    ->relationship('organisasi', 'nama_organisasi')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama_organisasi')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('jenis')
                            ->maxLength(50),
                    ]),
                Forms\Components\TextInput::make('jabatan')
                    ->maxLength(100),
                Forms\Components\TextInput::make('tahun_masuk')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y')),
                Forms\Components\TextInput::make('tahun_keluar')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y') + 10),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organisasi.nama_organisasi')
                    ->label('Organisasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organisasi.jenis')
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('jabatan'),
                Tables\Columns\TextColumn::make('tahun_masuk'),
                Tables\Columns\TextColumn::make('tahun_keluar'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
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
