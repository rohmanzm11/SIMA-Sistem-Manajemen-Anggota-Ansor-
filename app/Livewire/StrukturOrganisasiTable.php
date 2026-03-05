<?php

namespace App\Livewire;

use App\Models\Level;
use App\Models\StrukturOrganisasi;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class StrukturOrganisasiTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(StrukturOrganisasi::query()->with(['anggota', 'level', 'jabatan']))
            ->heading('Struktur Organisasi')
            ->columns([
                Tables\Columns\TextColumn::make('anggota.nama_lengkap')
                    ->label('Anggota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('level.level_type')
                    ->label('Level Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PC'      => 'info',
                        'PAC'     => 'warning',
                        'RANTING' => 'success',
                        default   => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('level.nama_level')
                    ->label('Nama Level')
                    ->getStateUsing(fn($record) => $record->level?->nama_level ?? '—'),

                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('masa_khidmat_mulai')
                    ->label('Mulai')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('masa_khidmat_selesai')
                    ->label('Selesai')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('sk_nomor')
                    ->label('Nomor SK')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sk_tanggal')
                    ->label('Tanggal SK')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level_id')
                    ->label('Level')
                    ->options(function () {
                        return Level::all()->mapWithKeys(function ($level) {
                            $namaLevel = $level->nama_level ?? "{$level->level_type} #{$level->id}";
                            return [$level->id => "[{$level->level_type}] {$namaLevel}"];
                        });
                    }),

                Tables\Filters\SelectFilter::make('jabatan_id')
                    ->label('Jabatan')
                    ->relationship('jabatan', 'nama_jabatan')
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public function render()
    {
        return view('livewire.struktur-organisasi-table');
    }
}
