<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KtaResource\Pages;
use App\Models\Kta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KtaResource extends Resource
{
    protected static ?string $model = Kta::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'TEMPLATE KTA';

    protected static ?string $modelLabel = 'TEMPLATE KTA';

    protected static ?string $pluralModelLabel = 'KTA';

    protected static ?string $navigationGroup = 'Template Dokumen';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Batch KTA')
                    ->schema([
                        Forms\Components\TextInput::make('nama_batch')
                            ->label('Nama Batch')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Batch 2024-I')
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('tanggal_terbit')
                            ->label('Tanggal Terbit')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\DatePicker::make('tanggal_berlaku_sampai')
                            ->label('Berlaku Sampai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('tanggal_terbit'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\FileUpload::make('image')
                            ->label('Foto / Template KTA')
                            ->image()
                            ->imageEditor()
                            ->directory('kta-images')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Penandatangan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_ketua')
                            ->label('Nama Ketua')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('ttd_ketua')
                            ->label('Tanda Tangan Ketua')
                            ->image()
                            ->directory('ttd-kta')
                            ->maxSize(1024),

                        Forms\Components\TextInput::make('nama_sekretaris')
                            ->label('Nama Sekretaris')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('ttd_sekretaris')
                            ->label('Tanda Tangan Sekretaris')
                            ->image()
                            ->directory('ttd-kta')
                            ->maxSize(1024),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_batch')
                    ->label('Nama Batch')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_terbit')
                    ->label('Tanggal Terbit')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_berlaku_sampai')
                    ->label('Berlaku Sampai')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn(Kta $record): string => $record->isExpired() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('nama_ketua')
                    ->label('Nama Ketua')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_sekretaris')
                    ->label('Nama Sekretaris')
                    ->searchable()
                    ->sortable(),

                // Tables\Columns\ImageColumn::make('image')
                //     ->label('Foto')
                //     ->circular(),
                // // ->defaultImageUrl(url('/images/placeholder.png')),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),

                Tables\Filters\Filter::make('expired')
                    ->label('Sudah Kadaluarsa')
                    ->query(fn(Builder $query): Builder => $query->expired()),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Akan Kadaluarsa (30 hari)')
                    ->query(fn(Builder $query): Builder => $query->expiringSoon(30)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKtas::route('/'),
            'create' => Pages\CreateKta::route('/create'),
            'view' => Pages\ViewKta::route('/{record}'),
            'edit' => Pages\EditKta::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
