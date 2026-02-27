<?php

namespace App\Filament\Widgets;

use App\Models\RiwayatPendidikan;
use App\Models\SiswaDataPendaftar;
use App\Models\TahunAkademik;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class SiswaOverviewStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->can('View:SiswaOverviewStats');
    }

    protected function getStats(): array
    {
        $activeTahunId = $this->filters['tahun_akademik'] ?? null;
        $tahunAkademik = $activeTahunId ? TahunAkademik::find($activeTahunId) : null;
        $tahunNama = $tahunAkademik?->nama;

        $totalAktif = RiwayatPendidikan::query()
            ->where('ro_status_siswa', 37)
            ->when($tahunNama, fn(Builder $query) => $query->whereHas('akademikKrs', fn($q) => $q->where('kode_tahun', $tahunNama)))
            ->count();

        $totalPending = RiwayatPendidikan::query()
            ->whereIn('ro_status_siswa', [142, 43])
            ->when($activeTahunId, fn(Builder $query) => $query->where('id_tahun_akademik', $activeTahunId))
            ->count();

        $totalPendaftar = SiswaDataPendaftar::query()
            ->when($activeTahunId, fn(Builder $query) => $query->where('id_tahun_akademik', $activeTahunId))
            ->count();

        return [
            Stat::make('Total Mahasiswa Aktif', $totalAktif)
                ->description('Berdasarkan KRS ' . ($tahunNama ?? 'Semua Tahun'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Pending / Non-Aktif', $totalPending)
                ->description('Pendaftar lama atau non-aktif')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Calon Mahasiswa', $totalPendaftar)
                ->description('Data pendaftaran tahun ' . ($tahunNama ? substr($tahunNama, 0, 4) : 'Semua'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }
}
