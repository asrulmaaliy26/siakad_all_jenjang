<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use App\Models\RiwayatPendidikan;
use App\Models\SiswaDataPendaftar;
use App\Models\TahunAkademik;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class JurusanStudentStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Rincian Mahasiswa Per Program Studi';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->can('View:JurusanStudentStats');
    }

    public function table(Table $table): Table
    {
        $activeTahunId = $this->filters['tahun_akademik'] ?? null;
        $tahunAkademik = $activeTahunId ? TahunAkademik::find($activeTahunId) : null;
        $tahunNama = $tahunAkademik?->nama;

        return $table
            ->query(
                Jurusan::query()

                    ->withCount([
                        'riwayatPendidikan as total_aktif' => function (Builder $query) use ($tahunNama) {
                            $query->where('ro_status_siswa', 37); // Aktif
                            if ($tahunNama) {
                                $query->whereHas('akademikKrs', fn($q) => $q->where('kode_tahun', $tahunNama));
                            }
                        },
                        'riwayatPendidikan as total_pending' => function (Builder $query) use ($activeTahunId) {
                            $query->whereIn('ro_status_siswa', [142, 43]); // Pendaftar, Non-Aktif
                            if ($activeTahunId) {
                                // For pending, maybe they don't have KRS yet, so we check id_tahun_akademik or just skip year filter
                                $query->where('id_tahun_akademik', $activeTahunId);
                            }
                        },
                        'pendaftar as total_pendaftar_baru' => function (Builder $query) use ($activeTahunId) {
                            if ($activeTahunId) {
                                $query->where('id_tahun_akademik', $activeTahunId);
                            }
                        },
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Program Studi')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn(Jurusan $record): string => $record->fakultas->nama ?? 'Tanpa Fakultas'),

                Tables\Columns\TextColumn::make('total_aktif')
                    ->label('Mhs. Aktif')
                    ->sortable()
                    ->icon('heroicon-m-user-group')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state . ' Orang'),

                Tables\Columns\TextColumn::make('total_pending')
                    ->label('Pending / Non-Aktif')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state . ' Orang'),

                Tables\Columns\TextColumn::make('total_pendaftar_baru')
                    ->label('Calon Mahasiswa')
                    ->sortable()
                    ->icon('heroicon-m-user-plus')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state . ' Orang')
                    ->description('Data Pendaftaran'),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('total_aktif', 'desc')
            ->emptyStateHeading('Tidak ada data statistik untuk periode ini');
    }
}
