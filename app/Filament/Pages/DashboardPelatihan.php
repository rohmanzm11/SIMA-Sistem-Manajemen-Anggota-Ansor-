<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PelatihanResource;
use App\Models\Anggota;
use App\Models\AnggotaPelatihan;
use App\Models\Pelatihan;
use App\Models\PelatihanDetail;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class DashboardPelatihan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard Pelatihan';
    protected static ?string $navigationGroup = 'Pelatihan & Sertifikasi';
    protected static ?int $navigationSort = 0;
    protected static ?string $title = 'Dashboard Pelatihan';
    protected static string $view = 'filament.pages.dashboard-pelatihan';

    /** Pelatihan yang sedang dipilih (tersimpan di URL ?selectedPelatihanId=1) */
    #[Url]
    public ?int $selectedPelatihanId = null;

    /** Cache ringkasan peserta untuk satu request */
    protected ?array $hasilCache = null;

    // ------------------------------------------------------------------
    // INPUT PELATIHAN (modal, memakai form yang sama dengan Resource)
    // ------------------------------------------------------------------
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('inputPelatihan')
                ->label('Input Pelatihan')
                ->icon('heroicon-o-plus')
                ->model(Pelatihan::class)
                ->form(PelatihanResource::formSchema())
                ->modalHeading('Input Pelatihan')
                ->modalWidth(MaxWidth::SevenExtraLarge)
                ->createAnother(false)
                ->successNotificationTitle('Pelatihan berhasil disimpan'),
        ];
    }

    public function pilihPelatihan(?int $id): void
    {
        // Klik kartu yang sama sekali lagi = batal pilih
        $this->selectedPelatihanId = $this->selectedPelatihanId === $id ? null : $id;
        $this->hasilCache = null;
        $this->resetTable();
    }

    // ------------------------------------------------------------------
    // ATURAN KELULUSAN
    // ------------------------------------------------------------------

    /** Satu materi dianggap lulus: hadir DAN skor >= 60% skor maksimal. */
    protected static function materiLulus(Collection $rowsPeserta, PelatihanDetail $detail): bool
    {
        $row = $rowsPeserta->firstWhere('pelatihan_detail_id', $detail->id);

        return $row
            && $row->status_kehadiran === 'Hadir'
            && $row->isPassed();
    }

    /** Peserta lulus pelatihan: lulus di SEMUA materi aktif. */
    protected static function apakahLulus(Collection $rowsPeserta, Collection $materiWajib): bool
    {
        if ($materiWajib->isEmpty()) {
            return false;
        }

        return $materiWajib->every(fn (PelatihanDetail $d) => static::materiLulus($rowsPeserta, $d));
    }

    // ------------------------------------------------------------------
    // WIDGET RINGKASAN PER PELATIHAN (dipakai di blade)
    // ------------------------------------------------------------------
    public function getPelatihanStats(): Collection
    {
        $pelatihans = Pelatihan::query()
            ->with('pelatihanDetails')
            ->orderByDesc('tanggal_mulai')
            ->get();

        $detailIds = $pelatihans->flatMap->pelatihanDetails->pluck('id');

        // Satu query untuk semua peserta, dikelompokkan per pelatihan
        $records = AnggotaPelatihan::query()
            ->with('pelatihanDetail.materi')
            ->whereIn('pelatihan_detail_id', $detailIds)
            ->get()
            ->groupBy(fn (AnggotaPelatihan $r) => $r->pelatihanDetail->pelatihan_id);

        return $pelatihans->map(function (Pelatihan $p) use ($records) {
            $wajib = $p->pelatihanDetails->where('is_active', true);
            $perPeserta = ($records[$p->id] ?? collect())->groupBy('anggota_id');

            $total = $perPeserta->count();
            $lulus = $perPeserta
                ->filter(fn (Collection $rows) => static::apakahLulus($rows, $wajib))
                ->count();

            return [
                'id'            => $p->id,
                'nama'          => $p->nama_pelatihan,
                'tempat'        => $p->tempat,
                'tanggal'       => $p->tanggal_mulai?->translatedFormat('d M Y')
                    . ($p->tanggal_selesai && ! $p->tanggal_selesai->isSameDay($p->tanggal_mulai)
                        ? ' – ' . $p->tanggal_selesai->translatedFormat('d M Y')
                        : ''),
                'jumlah_materi' => $wajib->count(),
                'peserta'       => $total,
                'lulus'         => $lulus,
                'ditolak'       => $total - $lulus,
                'persen_lulus'  => $total > 0 ? round($lulus / $total * 100) : 0,
            ];
        });
    }

    /**
     * Ringkasan per peserta untuk pelatihan yang dipilih:
     * [anggota_id => ['lulus_materi' => n, 'total_materi' => m, 'lulus' => bool]]
     */
    protected function ringkasanPeserta(): array
    {
        if ($this->hasilCache !== null) {
            return $this->hasilCache;
        }

        if (! $this->selectedPelatihanId) {
            return $this->hasilCache = [];
        }

        $wajib = $this->materiAktif();

        $rows = AnggotaPelatihan::query()
            ->with('pelatihanDetail.materi')
            ->whereIn('pelatihan_detail_id', $wajib->pluck('id'))
            ->get()
            ->groupBy('anggota_id');

        return $this->hasilCache = $rows
            ->map(fn (Collection $r) => [
                'lulus_materi' => $wajib->filter(fn (PelatihanDetail $d) => static::materiLulus($r, $d))->count(),
                'total_materi' => $wajib->count(),
                'lulus'        => static::apakahLulus($r, $wajib),
            ])
            ->all();
    }

    protected function materiAktif(): Collection
    {
        return PelatihanDetail::query()
            ->where('pelatihan_id', $this->selectedPelatihanId)
            ->where('is_active', true)
            ->get();
    }

    // ------------------------------------------------------------------
    // TABEL PESERTA (satu baris per anggota, materi ada di panel samping)
    // ------------------------------------------------------------------
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Anggota::query()
                ->whereHas('pelatihanRecords', fn (Builder $q) => $q->whereHas(
                    'pelatihanDetail',
                    fn (Builder $d) => $d->where('pelatihan_id', $this->selectedPelatihanId ?? 0)
                ))
            )
            ->heading(fn (): string => 'Peserta: ' . (Pelatihan::find($this->selectedPelatihanId)?->nama_pelatihan ?? '-'))
            ->emptyStateHeading('Belum ada peserta')
            ->emptyStateDescription('Klik Tambah Peserta untuk mendaftarkan anggota ke pelatihan ini.')
            ->defaultSort('nama_lengkap')
            ->recordAction('materi')   // klik baris = buka daftar materi
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Peserta')
                    ->description(fn (Anggota $r): string => $r->nia ?? '—')
                    ->searchable(['nama_lengkap', 'nia'])
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('materi_ringkas')
                    ->label('Materi')
                    ->getStateUsing(function (Anggota $r): string {
                        $x = $this->ringkasanPeserta()[$r->id] ?? null;

                        return $x ? "{$x['lulus_materi']}/{$x['total_materi']} lulus" : '—';
                    })
                    ->badge()
                    ->color(function (Anggota $r): string {
                        $x = $this->ringkasanPeserta()[$r->id] ?? null;

                        return match (true) {
                            ! $x                                => 'gray',
                            $x['lulus']                         => 'success',
                            $x['lulus_materi'] > 0              => 'warning',
                            default                             => 'danger',
                        };
                    }),

                Tables\Columns\TextColumn::make('hasil_akhir')
                    ->label('Hasil')
                    ->getStateUsing(fn (Anggota $r): string => ($this->ringkasanPeserta()[$r->id]['lulus'] ?? false)
                        ? 'Lulus'
                        : 'Belum memenuhi syarat')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Lulus' ? 'success' : 'danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hasil')
                    ->label('Hasil')
                    ->options(['lulus' => 'Lulus', 'belum' => 'Belum memenuhi syarat'])
                    ->query(function (Builder $query, array $data): Builder {
                        $pilih = $data['value'] ?? null;

                        if (! $pilih) {
                            return $query;
                        }

                        $ids = collect($this->ringkasanPeserta())
                            ->filter(fn (array $r) => $r['lulus'] === ($pilih === 'lulus'))
                            ->keys();

                        return $query->whereIn('anggotas.id', $ids);
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('tambahPeserta')
                    ->label('Tambah Peserta')
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading('Tambah Peserta')
                    ->modalDescription('Peserta otomatis didaftarkan ke semua materi aktif pada pelatihan ini.')
                    ->form([
                        Forms\Components\Select::make('anggota_ids')
                            ->label('Anggota')
                            ->multiple()
                            ->searchable()
                            ->required()
                            ->getSearchResultsUsing(fn (string $search): array => Anggota::query()
                                ->where(fn ($q) => $q
                                    ->where('nama_lengkap', 'like', "%{$search}%")
                                    ->orWhere('nia', 'like', "%{$search}%"))
                                ->limit(50)
                                ->pluck('nama_lengkap', 'id')
                                ->all())
                            ->getOptionLabelsUsing(fn (array $values): array => Anggota::query()
                                ->whereIn('id', $values)
                                ->pluck('nama_lengkap', 'id')
                                ->all()),
                    ])
                    ->action(function (array $data): void {
                        $detailIds = $this->materiAktif()->pluck('id');

                        foreach ($data['anggota_ids'] as $anggotaId) {
                            foreach ($detailIds as $detailId) {
                                AnggotaPelatihan::firstOrCreate(
                                    ['anggota_id' => $anggotaId, 'pelatihan_detail_id' => $detailId],
                                    ['status_kehadiran' => 'Hadir'],
                                );
                            }
                        }

                        $this->hasilCache = null;

                        Notification::make()->title('Peserta ditambahkan')->success()->send();
                    }),
            ])
            ->actions([
                // Satu menu untuk semua materi peserta: lihat sekaligus isi kehadiran & skor
                Tables\Actions\Action::make('materi')
                    ->label('Materi')
                    ->icon('heroicon-o-list-bullet')
                    ->color('gray')
                    ->slideOver()
                    ->modalWidth(MaxWidth::Large)
                    ->modalHeading(fn (Anggota $record): string => 'Materi: ' . $record->nama_lengkap)
                    ->modalSubmitActionLabel('Simpan nilai')
                    ->fillForm(fn (Anggota $record): array => ['materi' => $this->dataMateriPeserta($record)])
                    ->form([
                        Forms\Components\Repeater::make('materi')
                            ->label('')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(2)
                            ->itemLabel(fn (array $state): string => ($state['nama_materi'] ?? 'Materi')
                                . ' · ' . ($state['hasil'] ?? ''))
                            ->schema([
                                Forms\Components\Hidden::make('id'),
                                Forms\Components\Hidden::make('nama_materi'),
                                Forms\Components\Hidden::make('hasil'),
                                Forms\Components\Hidden::make('skor_maksimal'),

                                Forms\Components\Select::make('status_kehadiran')
                                    ->label('Kehadiran')
                                    ->options(['Hadir' => 'Hadir', 'Tidak Hadir' => 'Tidak Hadir'])
                                    ->required(),

                                Forms\Components\TextInput::make('skor')
                                    ->label('Skor')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix(fn (Get $get): string => '/ ' . ($get('skor_maksimal') ?? 100)),
                            ]),
                    ])
                    ->action(function (array $data, Anggota $record): void {
                        foreach ($data['materi'] ?? [] as $row) {
                            AnggotaPelatihan::query()
                                ->whereKey($row['id'])
                                ->where('anggota_id', $record->id)
                                ->update([
                                    'status_kehadiran' => $row['status_kehadiran'],
                                    'skor'             => ($row['skor'] ?? '') === '' ? null : $row['skor'],
                                ]);
                        }

                        $this->hasilCache = null;

                        Notification::make()->title('Nilai disimpan')->success()->send();
                    }),

                // Hanya mengeluarkan dari pelatihan ini, data anggota tidak dihapus
                Tables\Actions\Action::make('keluarkan')
                    ->label('Keluarkan')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->iconButton()
                    ->tooltip('Keluarkan dari pelatihan')
                    ->requiresConfirmation()
                    ->modalHeading('Keluarkan dari pelatihan?')
                    ->modalDescription('Data kehadiran dan skor peserta di pelatihan ini akan dihapus. Data anggota tidak terhapus.')
                    ->action(function (Anggota $record): void {
                        AnggotaPelatihan::query()
                            ->where('anggota_id', $record->id)
                            ->whereIn(
                                'pelatihan_detail_id',
                                PelatihanDetail::where('pelatihan_id', $this->selectedPelatihanId)->pluck('id')
                            )
                            ->delete();

                        $this->hasilCache = null;

                        Notification::make()->title('Peserta dikeluarkan')->success()->send();
                    }),
            ])
            ->paginated([10, 25, 50]);
    }

    /** Data awal form "Materi" untuk satu peserta. */
    protected function dataMateriPeserta(Anggota $anggota): array
    {
        return AnggotaPelatihan::query()
            ->with('pelatihanDetail.materi')
            ->where('anggota_id', $anggota->id)
            ->whereHas('pelatihanDetail', fn (Builder $q) => $q
                ->where('pelatihan_id', $this->selectedPelatihanId)
                ->where('is_active', true))
            ->get()
            ->map(fn (AnggotaPelatihan $r): array => [
                'id'               => $r->id,
                'nama_materi'      => $r->pelatihanDetail?->materi?->nama_materi ?? 'Materi',
                'hasil'            => ($r->status_kehadiran === 'Hadir' && $r->isPassed())
                    ? 'Lulus'
                    : 'Belum memenuhi syarat',
                'skor_maksimal'    => (float) ($r->pelatihanDetail?->materi?->skor_maksimal ?? 100),
                'status_kehadiran' => $r->status_kehadiran,
                'skor'             => $r->skor,
            ])
            ->values()
            ->all();
    }
}