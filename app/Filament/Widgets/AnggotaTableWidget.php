<?php

namespace App\Filament\Widgets;

use App\Models\Anggota;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AnggotaTableWidget extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Anggota Terbaru';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Anggota::query()
                    ->with(['kecamatan', 'desa'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(asset('images/default-avatar.png'))
                    ->size(32),

                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->weight('semibold')
                    ->searchable()
                    ->url(fn(Anggota $record) => route('filament.admin.resources.anggotas.view', $record->id)),

                Tables\Columns\TextColumn::make('kecamatan.nama_kecamatan')
                    ->label('Kecamatan')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status_verifikasi')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Diverifikasi' => 'success',
                        'Ditolak'      => 'danger',
                        default        => 'warning',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Daftar')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn(Anggota $record) => route('filament.admin.resources.anggotas.view', $record->id)),

                Tables\Actions\Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn(Anggota $record): bool => $record->status_verifikasi === 'Pending')
                    ->requiresConfirmation()
                    ->action(function (Anggota $record): void {
                        $record->update([
                            'status_verifikasi'  => 'Diverifikasi',
                            'tanggal_verifikasi' => now(),
                        ]);
                    }),
            ])
            ->paginated(false)
            ->striped();
    }
}
