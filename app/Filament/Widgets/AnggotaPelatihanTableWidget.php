<?php

namespace App\Filament\Widgets;

use App\Models\AnggotaPelatihan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;

class AnggotaPelatihanTableWidget extends BaseTableWidget
{
    // use HasWidgetShield;
    use HasWidgetShield;
    protected static ?string $heading = 'Riwayat Pelatihan Anggota';
    protected static ?string $description = 'Daftar pelatihan yang telah diikuti anggota';
    protected int | string | array $columnSpan = 3;
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(AnggotaPelatihan::query()->with(['anggota', 'pelatihanDetail.pelatihan', 'pelatihanDetail.materi']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('anggota.nama_lengkap')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('pelatihanDetail.pelatihan.nama_pelatihan')
                    ->label('Pelatihan')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('pelatihanDetail.materi.nama_materi')
                    ->label('Materi')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status_kehadiran')
                    ->label('Kehadiran')
                    ->colors([
                        'success' => 'Hadir',
                        'danger' => 'Tidak Hadir',
                        'warning' => 'Izin',
                        'gray' => 'Belum Dikonfirmasi',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('skor')
                    ->label('Skor')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\BadgeColumn::make('status_lulus')
                    ->label('Status')
                    ->getStateUsing(function (AnggotaPelatihan $record) {
                        return $record->isPassed() ? 'Lulus' : 'Tidak Lulus';
                    })
                    ->colors([
                        'success' => 'Lulus',
                        'danger' => 'Tidak Lulus',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('sertifikat_nomor')
                    ->label('Sertifikat')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_terbit_sertifikat')
                    ->label('Tanggal Sertifikat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kehadiran')
                    ->label('Filter Kehadiran')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Tidak Hadir' => 'Tidak Hadir',
                        'Izin' => 'Izin',
                        'Belum Dikonfirmasi' => 'Belum Dikonfirmasi',
                    ])
                    ->placeholder('Semua Status'),

                Tables\Filters\TernaryFilter::make('status_lulus')
                    ->label('Filter Kelulusan')
                    ->queries(
                        true: fn(Builder $query) => $query->where('skor', '>=', 60),
                        false: fn(Builder $query) => $query->where('skor', '<', 60)->orWhereNull('skor'),
                    ),

                Tables\Filters\TernaryFilter::make('sertifikat')
                    ->label('Ada Sertifikat')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('sertifikat_nomor'),
                        false: fn(Builder $query) => $query->whereNull('sertifikat_nomor'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
