<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RantingResource\Pages;
use App\Filament\Resources\RantingResource\RelationManagers;
use App\Models\Ranting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RantingResource extends Resource
{
    protected static ?string $model = Ranting::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';
    protected static ?string $navigationGroup = 'Organisasi';
    protected static ?string $navigationLabel = 'Ranting';
    protected static ?string $modelLabel = 'Ranting';
    protected static ?string $pluralModelLabel = 'Ranting';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pc_id')
                    ->label('PC')
                    ->relationship('pac.pc', 'nama_pc') // ← sesuaikan kolom nama di tabel pcs
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('pac_id')
                    ->label('PAC')
                    ->relationship('pac', 'nama_pac') // ← sesuaikan kolom nama di tabel pacs
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('nama_ranting')
                    ->label('Nama Ranting')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pac.pc.nama_pc')->label('PC'),
                Tables\Columns\TextColumn::make('pac.nama_pac')->label('PAC'),
                Tables\Columns\TextColumn::make('nama_ranting')->label('Nama Ranting'),
            ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRantings::route('/'),
        ];
    }
}
