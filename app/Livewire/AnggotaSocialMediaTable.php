<?php
// app/Livewire/AnggotaSocialMediaTable.php

namespace App\Livewire;

use App\Models\AnggotaSocialMedia;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class AnggotaSocialMediaTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(AnggotaSocialMedia::query()->with(['anggota', 'socialMedia']))
            ->heading('Data Social Media Anggota')
            ->columns([
                Tables\Columns\TextColumn::make('anggota.nama_lengkap')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('socialMedia.platform_name')
                    ->label('Platform')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->url(fn($record) => $record->url)
                    ->openUrlInNewTab()
                    ->limit(40)
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('social_media_id')
                    ->label('Platform')
                    ->relationship('socialMedia', 'platform_name'),
            ])
            ->paginated([10, 25, 50]);
    }

    public function render()
    {
        return view('livewire.anggota-social-media-table');
    }
}
