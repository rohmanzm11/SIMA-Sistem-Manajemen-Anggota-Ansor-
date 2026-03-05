<?php

namespace App\Filament\Resources\KtaResource\Pages;

use App\Filament\Resources\KtaResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewKta extends ViewRecord
{
    protected static string $resource = KtaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Anggota')
                    ->schema([
                        Infolists\Components\ImageEntry::make('image')
                            ->label('Foto KTA')
                            ->height(200)
                            ->defaultImageUrl(url('/images/placeholder.png'))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('anggota.nama')
                            ->label('Nama Anggota')
                            ->weight(FontWeight::Bold)
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),

                        Infolists\Components\TextEntry::make('anggota.nia')
                            ->label('Nomor KTA')
                            ->copyable()
                            ->copyMessage('Nomor KTA disalin!')
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail KTA')
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal_terbit')
                            ->label('Tanggal Terbit')
                            ->date('d F Y'),

                        Infolists\Components\TextEntry::make('tanggal_berlaku_sampai')
                            ->label('Berlaku Sampai')
                            ->date('d F Y')
                            ->color(fn($record) => $record->isExpired() ? 'danger' : 'success')
                            ->weight(FontWeight::Bold),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Status Aktif')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('tanggal_berlaku_sampai')
                            ->label('Status KTA')
                            ->formatStateUsing(function ($record) {
                                if (!$record->is_active) {
                                    return 'Tidak Aktif';
                                }
                                if ($record->isExpired()) {
                                    return 'Kadaluarsa';
                                }
                                $sisa = now()->diffInDays($record->tanggal_berlaku_sampai);
                                return "Valid ({$sisa} hari lagi)";
                            })
                            ->badge()
                            ->color(function ($record) {
                                if (!$record->is_active) return 'gray';
                                if ($record->isExpired()) return 'danger';
                                $sisa = now()->diffInDays($record->tanggal_berlaku_sampai);
                                return $sisa <= 30 ? 'warning' : 'success';
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y, H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime('d F Y, H:i'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
