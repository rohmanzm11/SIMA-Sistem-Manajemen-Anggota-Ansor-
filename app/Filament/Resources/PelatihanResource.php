<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelatihanResource\Pages;
use App\Models\Materi;
use App\Models\Pelatihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PelatihanResource extends Resource
{
    protected static ?string $model = Pelatihan::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Pelatihan';

    protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';

    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Pelatihan';
    protected static ?string $pluralModelLabel = 'Pelatihan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelatihan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_pelatihan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Sesi Pelatihan')
                    ->description('Tambahkan satu atau lebih sesi untuk pelatihan ini.')
                    ->schema([
                        Forms\Components\Repeater::make('pelatihanDetails')
                            ->label('')
                            ->relationship('pelatihanDetails')
                            ->schema([
                                Forms\Components\Select::make('materi_id')
                                    ->label('Materi')
                                    ->options(Materi::query()->pluck('nama_materi', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('pengajar')
                                    ->label('Pengajar')
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('tempat')
                                    ->label('Tempat')
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\DatePicker::make('tanggal')
                                    ->label('Tanggal')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(2),

                                Forms\Components\TimePicker::make('jam_mulai')
                                    ->label('Jam Mulai')
                                    ->seconds(false)
                                    ->columnSpan(1),

                                Forms\Components\TimePicker::make('jam_selesai')
                                    ->label('Jam Selesai')
                                    ->seconds(false)
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->addActionLabel('+ Tambah Sesi')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->collapseAllAction(
                                fn(Forms\Components\Actions\Action $action) => $action->label('Tutup Semua')
                            )
                            ->expandAllAction(
                                fn(Forms\Components\Actions\Action $action) => $action->label('Buka Semua')
                            )
                            ->itemLabel(
                                fn(array $state): ?string => ($state['tanggal'] ?? null)
                                    ? ($state['pengajar'] ?? 'Sesi') . ' — ' . \Carbon\Carbon::parse($state['tanggal'])->format('d/m/Y')
                                    : ($state['pengajar'] ?? 'Sesi Baru')
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_pelatihan')
                    ->label('Nama Pelatihan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pelatihanDetails_count')
                    ->counts('pelatihanDetails')
                    ->label('Jumlah Sesi')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('pelatihanDetails_min_tanggal')
                    ->label('Sesi Terdekat')
                    ->getStateUsing(function ($record) {
                        $next = $record->pelatihanDetails()
                            ->whereNotNull('tanggal')
                            ->where('tanggal', '>=', now()->toDateString())
                            ->orderBy('tanggal')
                            ->first();

                        return $next
                            ? \Carbon\Carbon::parse($next->tanggal)->translatedFormat('d M Y')
                            : '—';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->withMin('pelatihanDetails', 'tanggal')
                            ->orderBy('pelatihan_details_min_tanggal', $direction);
                    }),

                Tables\Columns\TextColumn::make('pelatihanDetails_pengajar')
                    ->label('Pengajar')
                    ->getStateUsing(function ($record) {
                        return $record->pelatihanDetails()
                            ->whereNotNull('pengajar')
                            ->orderBy('tanggal')
                            ->pluck('pengajar')
                            ->unique()
                            ->join(', ') ?: '—';
                    }),

                Tables\Columns\IconColumn::make('sertifikat_template')
                    ->label('Template')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'edit'   => Pages\EditPelatihan::route('/{record}/edit'),
        ];
    }
}
