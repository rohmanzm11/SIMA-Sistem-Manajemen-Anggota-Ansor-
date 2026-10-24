<?php

namespace App\Filament\Resources\AnggotaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KtasRelationManager extends RelationManager
{
    protected static string $relationship = 'ktas';

    protected static ?string $title = 'Kartu Tanda Anggota';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_kta')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                Forms\Components\DatePicker::make('tanggal_terbit')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('tanggal_berlaku_sampai')
                    ->required()
                    ->default(now()->addYears(5)),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_kta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_terbit')
                    ->date(),
                Tables\Columns\TextColumn::make('tanggal_berlaku_sampai')
                    ->date()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : 'success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
