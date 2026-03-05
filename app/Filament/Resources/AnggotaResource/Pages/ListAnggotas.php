<?php

namespace App\Filament\Resources\AnggotaResource\Pages;

use App\Filament\Resources\AnggotaResource;
use App\Models\Anggota;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAnggotas extends ListRecords
{
    protected static string $resource = AnggotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Anggota'),
        ];
    }

    /**
     * Tab filter berdasarkan status verifikasi
     */
    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(Anggota::count()),

            'pending' => Tab::make('Pending')
                ->badge(Anggota::pending()->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->pending()),

            'diverifikasi' => Tab::make('Diverifikasi')
                ->badge(Anggota::verified()->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->verified()),

            'ditolak' => Tab::make('Ditolak')
                ->badge(Anggota::rejected()->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->rejected()),
        ];
    }
}
