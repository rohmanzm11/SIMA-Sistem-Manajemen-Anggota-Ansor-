<?php

namespace App\Filament\Resources\AnggotaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StrukturOrganisasiRelationManager extends RelationManager
{
    protected static string $relationship = 'strukturOrganisasi';

    protected static ?string $title = 'Jabatan dalam Struktur';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('level_id')
                    ->relationship('level', 'id')
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "{$record->level_type} - {$record->nama_level}"
                    ),
                Forms\Components\Select::make('jabatan_id')
                    ->relationship('jabatan', 'nama_jabatan')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('masa_khidmat_mulai')
                    ->required(),
                Forms\Components\DatePicker::make('masa_khidmat_selesai'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Forms\Components\TextInput::make('sk_nomor')
                    ->maxLength(100),
                Forms\Components\DatePicker::make('sk_tanggal'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level.level_type')
                    ->label('Level'),
                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan'),
                Tables\Columns\TextColumn::make('masa_khidmat_mulai')
                    ->date(),
                Tables\Columns\TextColumn::make('masa_khidmat_selesai')
                    ->date(),
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
