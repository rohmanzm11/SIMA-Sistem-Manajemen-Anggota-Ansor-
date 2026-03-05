<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacResource\Pages;
use App\Filament\Resources\PacResource\RelationManagers;
use App\Models\Pac;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PacResource extends Resource
{
    protected static ?string $model = Pac::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';
    protected static ?string $navigationGroup = 'Organisasi';
    protected static ?string $navigationLabel = 'PAC';
    protected static ?string $modelLabel = 'PAC';
    protected static ?string $pluralModelLabel = 'PAC';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pc_id')
                    ->label('PC')
                    ->relationship('pc', 'nama_pc') // ← sesuaikan kolom nama di tabel pcs
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('nama_pac')
                    ->label('Nama PAC')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pc.nama_pc')->label('PC'), // Menampilkan nama PC terkait
                Tables\Columns\TextColumn::make('nama_pac')->label('Nama PAC'),
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
            'index' => Pages\ManagePacs::route('/'),
        ];
    }
}
