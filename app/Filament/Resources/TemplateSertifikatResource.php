<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateSertifikatResource\Pages;
use App\Models\TemplateSertifikat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TemplateSertifikatResource extends Resource
{
    protected static ?string $model = TemplateSertifikat::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Template Dokumen';
    protected static ?int $navigationSort = 1;
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Batch')
                    ->schema([
                        Forms\Components\TextInput::make('nama_batch')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tanggal_terbit')
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_berlaku_sampai')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Penandatangan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_ketua')->required(),
                        Forms\Components\FileUpload::make('ttd_ketua')
                            ->image()
                            ->directory('signatures')
                            ->required(),
                        Forms\Components\TextInput::make('nama_sekretaris')->required(),
                        Forms\Components\FileUpload::make('ttd_sekretaris')
                            ->image()
                            ->directory('signatures')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Desain Sertifikat')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Background Sertifikat')
                            ->image()
                            ->directory('templates'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_batch')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tanggal_terbit')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\ImageColumn::make('image')->label('Preview'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplateSertifikats::route('/'),
            'create' => Pages\CreateTemplateSertifikat::route('/create'),
            'edit' => Pages\EditTemplateSertifikat::route('/{record}/edit'),
        ];
    }
}
