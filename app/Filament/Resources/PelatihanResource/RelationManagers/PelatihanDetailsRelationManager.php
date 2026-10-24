<?php

namespace App\Filament\Resources\PelatihanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PelatihanDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'pelatihanDetails';

    protected static ?string $title = 'Jadwal Pelatihan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('materi_id')
                    ->relationship('materi', 'nama_materi')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama_materi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(3),
                        Forms\Components\TextInput::make('skor_maksimal')
                            ->numeric()
                            ->default(100),
                    ]),
                Forms\Components\TextInput::make('tempat')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal'),
                Forms\Components\TimePicker::make('jam_mulai'),
                Forms\Components\TimePicker::make('jam_selesai'),
                Forms\Components\TextInput::make('pengajar')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('materi.nama_materi')
                    ->label('Materi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tempat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->time(),
                Tables\Columns\TextColumn::make('jam_selesai')
                    ->time(),
                Tables\Columns\TextColumn::make('pengajar'),
                Tables\Columns\TextColumn::make('anggotaPelatihan_count')
                    ->counts('anggotaPelatihan')
                    ->label('Peserta'),
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
            ])
            ->defaultSort('tanggal', 'desc');
    }
}
