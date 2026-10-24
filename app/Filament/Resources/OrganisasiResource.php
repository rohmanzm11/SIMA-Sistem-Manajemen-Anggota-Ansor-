<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisasiResource\Pages;
use App\Filament\Resources\OrganisasiResource\RelationManagers;
use App\Models\Organisasi;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganisasiResource extends Resource
{
    protected static ?string $model = Organisasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $pluralModelLabel = 'Organisasi';
    protected static ?string $navigationLabel = 'Organisasi';
    protected static ?string $navigationGroup = 'Data Master';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nama Organisasi')
                    ->schema([
                        Forms\Components\TextInput::make('nama_organisasi')
                            ->required()
                            ->maxLength(255),
                    ]),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'Kemahasiswaan' => 'Kemahasiswaan',
                        'Kepemudaan' => 'Kepemudaan',
                        'Karyawan' => 'Karyawan',
                        'Politik' => 'Politik',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                tables\Columns\TextColumn::make('nama_organisasi')
                    ->label('Nama Organisasi')
                    ->searchable()
                    ->sortable(),
                tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Organisasi')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ManageOrganisasis::route('/'),
        ];
    }
}
