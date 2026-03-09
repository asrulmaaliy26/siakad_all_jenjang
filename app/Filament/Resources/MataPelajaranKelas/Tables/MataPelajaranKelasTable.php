<?php

namespace App\Filament\Resources\MataPelajaranKelas\Tables;

use App\Models\MataPelajaranKelas;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MataPelajaranKelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(function ($record) {
                /** @var \App\Models\User|null $user */
                $user = \Illuminate\Support\Facades\Auth::user();

                if ($user?->isMurid() && empty($record->id_dosen_data)) {
                    return null; // Mematikan tautan klik pada baris ini
                }

                // Gunakan default navigasi ke halaman view untuk baris yang diizinkan
                return \App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource::getUrl('view', ['record' => $record]);
            })
            ->columns([
                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('kelas.semester')
                    ->label('Semester')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // ── Hari & Jam dengan deteksi bentrok ──
                TextColumn::make('hari')
                    ->searchable()
                    ->toggleable()
                    ->color(fn($record, $livewire) => static::isBentrok($record, $livewire) ? 'danger' : null)
                    ->weight(fn($record, $livewire) => static::isBentrok($record, $livewire) ? 'bold' : null)
                    ->extraAttributes(fn($record, $livewire) => static::isBentrok($record, $livewire)
                        ? ['class' => 'blink-danger', 'title' => '⚠️ Jadwal bentrok! Hari, jam, ruang & pelaksanaan sama dengan kelas lain.']
                        : []),

                TextColumn::make('jam')
                    ->toggleable()
                    ->color(fn($record, $livewire) => static::isBentrok($record, $livewire) ? 'danger' : null)
                    ->weight(fn($record, $livewire) => static::isBentrok($record, $livewire) ? 'bold' : null)
                    ->extraAttributes(fn($record, $livewire) => static::isBentrok($record, $livewire)
                        ? ['class' => 'blink-danger', 'title' => '⚠️ Jadwal bentrok!']
                        : []),

                TextColumn::make('ruangKelas.nilai')
                    ->label('Ruang')
                    ->toggleable()
                    ->color(fn($record, $livewire) => static::isBentrok($record, $livewire) ? 'danger' : null)
                    ->weight(fn($record, $livewire) => static::isBentrok($record, $livewire) ? 'bold' : null)
                    ->extraAttributes(fn($record, $livewire) => static::isBentrok($record, $livewire)
                        ? ['class' => 'blink-danger', 'title' => '⚠️ Ruang bentrok!']
                        : []),

                TextColumn::make('jumlah')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('status_uts')
                    ->label('UTS')
                    ->boolean()
                    ->toggleable(),
                IconColumn::make('status_uas')
                    ->label('UAS')
                    ->boolean()
                    ->toggleable(),
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

                SelectFilter::make('id_dosen_data')
                    ->label('Dosen Pengajar')
                    ->relationship('dosenData', 'nama')
                    ->default(fn() => auth()->user()?->getDosenId())
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make()
                    ->disabled(function ($record) {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user?->isMurid() && empty($record->id_dosen_data);
                    })
                    ->tooltip(function ($record) {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if ($user?->isMurid() && empty($record->id_dosen_data)) {
                            return 'Belum dapat diakses, Dosen pengajar belum ditentukan.';
                        }
                        return null;
                    }),
                EditAction::make()
                    ->disabled(function () {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user?->isMurid();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make()
                        ->disabled(function () {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user?->isMurid();
                        }),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }

    // ================================================================
    // Deteksi Bentrok Jadwal
    // Warning muncul HANYA jika SEMUA 4 kondisi terpenuhi:
    //   1. Hari sama
    //   2. Jam overlap
    //   3. Ruang sama (ro_ruang_kelas sama)
    //   4. Pelaksanaan sama (ro_pelaksanaan_kelas sama)
    // ================================================================
    protected static array $bentrokIds      = [];
    protected static bool  $bentrokResolved = false;

    protected static function isBentrok($record, $livewire): bool
    {
        if (! static::$bentrokResolved) {
            static::resolveBentrok($livewire);
            static::$bentrokResolved = true;
        }
        return in_array($record->id, static::$bentrokIds, true);
    }

    protected static function resolveBentrok($livewire): void
    {
        static::$bentrokIds = [];

        try {
            $records = MataPelajaranKelas::query()
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
                        if (! static::jamOverlap($a->jam, $b->jam)) continue;

                        // Syarat 2: ruang harus sama
                        if ($a->ro_ruang_kelas != $b->ro_ruang_kelas) continue;

                        // Syarat 3: pelaksanaan harus sama
                        if ($a->ro_pelaksanaan_kelas != $b->ro_pelaksanaan_kelas) continue;

                        // Semua 4 kondisi terpenuhi → bentrok
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

    protected static function parseJam(string $jam): ?array
    {
        $jam = trim($jam);

        // Format "HH.MM-HH.MM" atau "HH:MM - HH:MM" (titik/titik-dua, dengan/tanpa spasi, dash/em-dash)
        if (preg_match('/^(\d{1,2})[.:](\d{2})\s*[-–]\s*(\d{1,2})[.:](\d{2})$/', $jam, $m)) {
            $start = (int)$m[1] * 60 + (int)$m[2];
            $end   = (int)$m[3] * 60 + (int)$m[4];
            if ($end <= $start) $end += 24 * 60;
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
