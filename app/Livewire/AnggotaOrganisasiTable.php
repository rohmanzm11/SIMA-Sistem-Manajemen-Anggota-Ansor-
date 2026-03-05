<?php
// app/Livewire/AnggotaOrganisasiTable.php

namespace App\Livewire;

use App\Models\AnggotaOrganisasi;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class AnggotaOrganisasiTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(AnggotaOrganisasi::query()->with(['anggota', 'organisasi']))
            ->heading('Data Organisasi Anggota')
            ->columns([
                Tables\Columns\TextColumn::make('anggota.nama_lengkap')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('organisasi.nama')
                    ->label('Organisasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->default('-'),

                Tables\Columns\TextColumn::make('tahun_masuk')
                    ->label('Tahun Masuk'),

                Tables\Columns\TextColumn::make('tahun_keluar')
                    ->label('Tahun Keluar')
                    ->default('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => route('filament.admin.pages.anggota-detail', ['id' => $record->id])),
            ])
            ->paginated([10, 25, 50]);
    }

    public function render()
    {
        return view('livewire.anggota-organisasi-table');
    }
}
