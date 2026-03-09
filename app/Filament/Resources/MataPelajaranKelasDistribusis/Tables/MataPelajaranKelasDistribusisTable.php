<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Tables;

use App\Filament\Resources\MataPelajaranKelasDistribusis\Actions\ExportMataPelajaranKelasAction;
use App\Filament\Resources\MataPelajaranKelasDistribusis\Actions\ImportMataPelajaranKelasAction;
use App\Models\Jurusan;
use App\Models\MataPelajaranKelasDistribusi;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MataPelajaranKelasDistribusisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: 'mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                //  TextColumn::make('id_mata_pelajaran_kurikulum')
                //     ->label('ID Kurikulum')
                //     ->numeric()
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('kelas.programKelas.nilai')
                    ->label('Program Kelas')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.kode_feeder')
                    ->label('Kode MK')
                    ->toggleable(),

                TextColumn::make('dosen.nama')
                    ->label('Dosen')
                    ->sortable()
                    ->toggleable(),

                // TextColumn::make('pengawas.nama')
                //     ->label('Pengawas')
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('ruangKelas.nilai')
                    ->label('Ruang')
                    ->sortable()
                    ->toggleable()
                    ->color(fn($record, $livewire) => static::isRuangBentrok($record, $livewire) ? 'danger' : null)
                    ->weight(fn($record, $livewire) => static::isRuangBentrok($record, $livewire) ? 'bold' : null)
                    ->extraAttributes(fn($record, $livewire) => static::isRuangBentrok($record, $livewire)
                        ? ['class' => 'blink-danger', 'title' => '⚠️ Ruang bentrok di hari & jam yang sama!']
                        : []),

                TextColumn::make('pelaksanaanKelas.nilai')
                    ->label('Pelaksanaan')
                    ->sortable()
                    ->toggleable()
                    ->color(fn($record, $livewire) => static::isPelaksanaanBentrok($record, $livewire) ? 'danger' : null)
                    ->weight(fn($record, $livewire) => static::isPelaksanaanBentrok($record, $livewire) ? 'bold' : null)
                    ->extraAttributes(fn($record, $livewire) => static::isPelaksanaanBentrok($record, $livewire)
                        ? ['class' => 'blink-danger', 'title' => '⚠️ Pelaksanaan bentrok di hari & jam yang sama!']
                        : []),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('hari')
                    ->label('Hari')
                    ->sortable()
                    ->toggleable()
                    ->color(fn($record, $livewire) => static::isJadwalBentrok($record, $livewire) ? 'danger' : null)
                    ->weight(fn($record, $livewire) => static::isJadwalBentrok($record, $livewire) ? 'bold' : null)
                    ->extraAttributes(fn($record, $livewire) => static::isJadwalBentrok($record, $livewire)
                        ? ['class' => 'blink-danger', 'title' => '⚠️ Jadwal bentrok dengan kelas lain!']
                        : []),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    // ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jam')
                    ->label('Jam')
                    ->sortable()
                    ->toggleable()
                    ->color(fn($record, $livewire) => static::isJadwalBentrok($record, $livewire) ? 'danger' : null)
                    ->weight(fn($record, $livewire) => static::isJadwalBentrok($record, $livewire) ? 'bold' : null)
                    ->extraAttributes(fn($record, $livewire) => static::isJadwalBentrok($record, $livewire)
                        ? ['class' => 'blink-danger', 'title' => '⚠️ Jadwal bentrok!']
                        : []),

                TextColumn::make('uts')
                    ->label('Jadwal UTS')
                    // ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('uas')
                    ->label('Jadwal UAS')
                    // ->dateTime()
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('status_uts')
                    ->label('Status UTS')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ])
                    ->toggleable(),

                BadgeColumn::make('status_uas')
                    ->label('Status UAS')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ])
                    ->toggleable(),

                TextColumn::make('ruang_uts')
                    ->label('Ruang UTS')
                    ->toggleable(),

                TextColumn::make('ruang_uas')
                    ->label('Ruang UAS')
                    ->toggleable(),

                TextColumn::make('link_kelas')
                    ->label('Link Kelas')
                    ->url(fn($record) => $record->link_kelas ?: null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('passcode')
                    ->label('Passcode')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(fn() => \App\Models\TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $query->whereHas('kelas', function ($q) use ($data) {
                            $q->where('id_tahun_akademik', $data['value']);
                        });
                    })
                    ->default(fn() => \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->searchable()
                    ->native(false),

                // Filter berdasarkan Mata Pelajaran
                SelectFilter::make('mata_pelajaran')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaranKurikulum.mataPelajaranMaster', 'nama')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Jurusan (dari relasi kelas → jurusan)
                SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->options(fn() => Jurusan::orderBy('nama')->pluck('nama', 'id')->toArray())
                    ->query(fn(Builder $query, array $data) => $query->when(
                        $data['value'] ?? null,
                        fn($q, $jurusanId) => $q->whereHas(
                            'kelas',
                            fn($k) => $k->where('id_jurusan', $jurusanId)
                        )
                    ))
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Program Kelas
                SelectFilter::make('program_kelas')
                    ->label('Program Kelas')
                    ->relationship('kelas.programKelas', 'nilai')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Dosen
                SelectFilter::make('dosen')
                    ->label('Dosen')
                    ->relationship('dosen', 'nama')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Hari
                SelectFilter::make('hari')
                    ->label('Hari')
                    ->options([
                        'Senin'  => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu'   => 'Rabu',
                        'Kamis'  => 'Kamis',
                        'Jumat'  => 'Jumat',
                        'Sabtu'  => 'Sabtu',
                        'Minggu' => 'Minggu',
                    ]),

                // Filter Status UTS
                SelectFilter::make('status_uts')
                    ->label('Status UTS')
                    ->options(['Y' => 'Aktif (Y)', 'N' => 'Nonaktif (N)']),

                // Filter Status UAS
                SelectFilter::make('status_uas')
                    ->label('Status UAS')
                    ->options(['Y' => 'Aktif (Y)', 'N' => 'Nonaktif (N)']),

                // Filter Tanggal range
                Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('tanggal_dari')
                            ->label('Tanggal Dari'),
                        \Filament\Forms\Components\DatePicker::make('tanggal_sampai')
                            ->label('Tanggal Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_dari'],
                                fn(Builder $q, $date) => $q->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_sampai'],
                                fn(Builder $q, $date) => $q->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->columns(2),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Bulk edit jumlah peserta
                    Action::make('bulk_edit_jumlah')
                        ->label('Edit Jumlah')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->bulk()
                        ->accessSelectedRecords()
                        ->modalHeading('Edit Jumlah Peserta')
                        ->modalDescription('Isi jumlah baru. Semua baris yang dipilih akan diperbarui.')
                        ->modalSubmitActionLabel('Simpan')
                        ->form([
                            TextInput::make('jumlah')
                                ->label('Jumlah Peserta')
                                ->numeric()
                                ->minValue(0)
                                ->required()
                                ->placeholder('Contoh: 35'),
                        ])
                        ->action(function (array $data, Action $action) {
                            $jumlah = (int) $data['jumlah'];
                            $ids    = $action->getSelectedRecords()->pluck('id');

                            $affected = MataPelajaranKelasDistribusi::whereIn('id', $ids)
                                ->update(['jumlah' => $jumlah]);

                            Notification::make()
                                ->title("{$affected} record diperbarui")
                                ->body("Jumlah peserta diset ke {$jumlah}.")
                                ->success()
                                ->send();
                        }),

                    // Export baris terpilih dengan pilihan kolom
                    ExportMataPelajaranKelasAction::makeBulk(),
                    // Export bawaan pxlrbt (semua kolom cepat)
                    // \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
                // Export semua data (dengan filter aktif) — pilih kolom
                ExportMataPelajaranKelasAction::make(),

                // Import / Update data — langsung diproses tanpa queue
                ImportMataPelajaranKelasAction::make(),
            ]);
    }

    /**
     * WARNING hanya muncul jika SEMUA 4 kondisi terpenuhi sekaligus:
     *   1. Hari sama
     *   2. Jam overlap
     *   3. Ruang sama (ro_ruang_kelas sama)
     *   4. Pelaksanaan sama (ro_pelaksanaan_kelas sama)
     *
     * Jika hanya hari+jam overlap tapi ruang/pelaksanaan berbeda → TIDAK ada warning.
     */
    protected static array $bentrokIds      = [];
    protected static bool  $bentrokResolved = false;

    protected static function resolveBentrok($livewire): void
    {
        static::$bentrokIds = [];

        try {
            $records = MataPelajaranKelasDistribusi::query()
                ->whereNotNull('hari')
                ->whereNotNull('jam')
                ->where('jam', '!=', '')
                ->whereNotNull('ro_ruang_kelas')
                ->whereNotNull('ro_pelaksanaan_kelas')
                ->get(['id', 'hari', 'jam', 'ro_ruang_kelas', 'ro_pelaksanaan_kelas']);

            $perHari = $records->groupBy('hari');

            foreach ($perHari as $hari => $group) {
                $list = $group->values();
                $n    = $list->count();

                for ($i = 0; $i < $n; $i++) {
                    for ($j = $i + 1; $j < $n; $j++) {
                        $a = $list[$i];
                        $b = $list[$j];

                        // Syarat 1: jam overlap
                        if (! static::jamOverlap($a->jam, $b->jam)) {
                            continue;
                        }

                        // Syarat 2: ruang harus sama
                        if ($a->ro_ruang_kelas != $b->ro_ruang_kelas) {
                            continue;
                        }

                        // Syarat 3: pelaksanaan harus sama
                        if ($a->ro_pelaksanaan_kelas != $b->ro_pelaksanaan_kelas) {
                            continue;
                        }

                        // Semua 4 kondisi terpenuhi → tandai bentrok
                        static::$bentrokIds[] = $a->id;
                        static::$bentrokIds[] = $b->id;
                    }
                }
            }

            static::$bentrokIds = array_unique(static::$bentrokIds);
        } catch (\Throwable $e) {
            static::$bentrokIds = [];
        }
    }

    // Semua kolom (hari, jam, ruang, pelaksanaan) pakai pengecekan yang sama
    protected static function isJadwalBentrok($record, $livewire): bool
    {
        if (! static::$bentrokResolved) {
            static::resolveBentrok($livewire);
            static::$bentrokResolved = true;
        }
        return in_array($record->id, static::$bentrokIds, true);
    }

    protected static function isRuangBentrok($record, $livewire): bool
    {
        return static::isJadwalBentrok($record, $livewire);
    }

    protected static function isPelaksanaanBentrok($record, $livewire): bool
    {
        return static::isJadwalBentrok($record, $livewire);
    }


    /**
     * Parse format jam: "HH:MM - HH:MM" → [start_minutes, end_minutes]
     * Juga tangani format "HH:MM" (tanpa end) → [start, start+60]
     */
    protected static function parseJam(string $jam): ?array
    {
        $jam = trim($jam);

        // Format "HH.MM-HH.MM" atau "HH:MM - HH:MM" (titik/titik-dua, dengan/tanpa spasi, dash/em-dash)
        if (preg_match('/^(\d{1,2})[.:](\d{2})\s*[-–]\s*(\d{1,2})[.:](\d{2})$/', $jam, $m)) {
            $start = (int)$m[1] * 60 + (int)$m[2];
            $end   = (int)$m[3] * 60 + (int)$m[4];
            if ($end <= $start) $end += 24 * 60; // lewat tengah malam
            return [$start, $end];
        }

        // Format "HH.MM" atau "HH:MM" saja → asumsi durasi 60 menit
        if (preg_match('/^(\d{1,2})[.:](\d{2})$/', $jam, $m)) {
            $start = (int)$m[1] * 60 + (int)$m[2];
            return [$start, $start + 60];
        }

        return null;
    }

    protected static function jamOverlap(string $jam1, string $jam2): bool
    {
        $a = static::parseJam($jam1);
        $b = static::parseJam($jam2);
        if (! $a || ! $b) return false;
        return $a[0] < $b[1] && $b[0] < $a[1];
    }
}
