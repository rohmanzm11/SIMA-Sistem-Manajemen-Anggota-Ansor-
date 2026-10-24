<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LevelResource\Pages;
use App\Models\Level;
use App\Models\Pac;
use App\Models\Pc;
use App\Models\Ranting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';

    protected static ?string $navigationLabel = 'Level Organisasi';

    protected static ?string $modelLabel = 'Level Organisasi';

    protected static ?string $pluralModelLabel = 'Level Organisasi';

    protected static ?string $navigationGroup = 'Organisasi';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Level')
                    ->schema([
                        Forms\Components\Select::make('level_type')
                            ->label('Tipe Level')
                            ->options([
                                'PC'      => 'PC (Pimpinan Cabang)',
                                'PAC'     => 'PAC (Pimpinan Anak Cabang)',
                                'RANTING' => 'Ranting',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('reference_id', null)),

                        Forms\Components\Select::make('reference_id')
                            ->label('Referensi')
                            ->required()
                            ->options(function (Get $get): array {
                                $levelType = $get('level_type');

                                return match ($levelType) {
                                    'PC'      => Pc::pluck('nama_pc', 'id')->toArray(),
                                    'PAC'     => Pac::pluck('nama_pac', 'id')->toArray(),
                                    'RANTING' => Ranting::pluck('nama_ranting', 'id')->toArray(),
                                    default   => [],
                                };
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder(
                                fn(Get $get): string => $get('level_type')
                                    ? 'Pilih referensi...'
                                    : 'Pilih tipe level terlebih dahulu'
                            )
                            ->disabled(fn(Get $get): bool => blank($get('level_type'))),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('level_type')
                    ->label('Tipe Level')
                    ->colors([
                        'primary' => 'PC',
                        'warning' => 'PAC',
                        'success' => 'RANTING',
                    ])
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_level')
                    ->label('Nama')
                    ->getStateUsing(function (Level $record): ?string {
                        return match ($record->level_type) {
                            'PC'      => Pc::find($record->reference_id)?->nama_pc,
                            'PAC'     => Pac::find($record->reference_id)?->nama_pac,
                            'RANTING' => Ranting::find($record->reference_id)?->nama_ranting,
                            default   => null,
                        };
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $pcIds      = Pc::where('nama_pc', 'like', "%{$search}%")->pluck('id');
                        $pacIds     = Pac::where('nama_pac', 'like', "%{$search}%")->pluck('id');
                        $rantingIds = Ranting::where('nama_ranting', 'like', "%{$search}%")->pluck('id');

                        return $query->where(function (Builder $query) use ($pcIds, $pacIds, $rantingIds) {
                            $query->where(fn($q) => $q->where('level_type', 'PC')->whereIn('reference_id', $pcIds))
                                ->orWhere(fn($q) => $q->where('level_type', 'PAC')->whereIn('reference_id', $pacIds))
                                ->orWhere(fn($q) => $q->where('level_type', 'RANTING')->whereIn('reference_id', $rantingIds));
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('reference_id', $direction);
                    }),

                Tables\Columns\TextColumn::make('reference_id')
                    ->label('Reference ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Tables\Columns\TextColumn::make('strukturOrganisasi_count')
                //     ->label('Struktur Organisasi')
                //     ->counts('strukturOrganisasi')
                //     ->badge()
                //     ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level_type')
                    ->label('Tipe Level')
                    ->options([
                        'PC'      => 'PC (Pimpinan Cabang)',
                        'PAC'     => 'PAC (Pimpinan Anak Cabang)',
                        'RANTING' => 'Ranting',
                    ])
                    ->placeholder('Semua Tipe'),
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
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Uncomment jika ingin menampilkan StrukturOrganisasi sebagai relation manager
            // RelationManagers\StrukturOrganisasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLevels::route('/'),
            'create' => Pages\CreateLevel::route('/create'),
            'view'   => Pages\ViewLevel::route('/{record}'),
            'edit'   => Pages\EditLevel::route('/{record}/edit'),
        ];
    }
}
