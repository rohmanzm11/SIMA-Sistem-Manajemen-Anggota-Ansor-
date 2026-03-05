<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MateriResource\Pages;
use App\Models\Materi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MateriResource extends Resource
{
    protected static ?string $model = Materi::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Materi';

    protected static ?string $modelLabel = 'Materi';

    protected static ?string $pluralModelLabel = 'Materi';
    protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';

    protected static ?string $slug = 'materi';

    // =========================================================
    // FORM
    // =========================================================
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informasi Materi')
                ->schema([
                    Forms\Components\TextInput::make('nama_materi')
                        ->label('Nama Materi')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->nullable()
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('skor_maksimal')
                        ->label('Skor Maksimal')
                        ->numeric()
                        ->default(100)
                        ->minValue(0)
                        ->maxValue(999.99)
                        ->step(0.01)
                        ->suffix('poin')
                        ->required(),
                ]),

        ]);
    }

    // =========================================================
    // TABLE
    // =========================================================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_materi')
                    ->label('Nama Materi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->placeholder('—')
                    ->tooltip(fn($record) => $record->deskripsi),

                Tables\Columns\TextColumn::make('skor_maksimal')
                    ->label('Skor Maksimal')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' poin')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    // =========================================================
    // PAGES — tanpa 'view' karena --generate tanpa --view
    // =========================================================
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMateris::route('/'),
            'create' => Pages\CreateMateri::route('/create'),
            'edit'   => Pages\EditMateri::route('/{record}/edit'),
        ];
    }
}
