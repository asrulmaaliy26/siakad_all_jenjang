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
        $tahunNamaBare  = $tahunAkademik ? $tahunAkademik->getRawOriginal('nama') : null; // nama asli tanpa accessor (e.g. "2025/2026")
        $tahunLabel     = $tahunNamaBare ?? 'Semua Tahun';

        // Kumpulkan semua ID tahun akademik yang punya nama tahun sama
        // (mencakup Ganjil & Genap dalam satu tahun, e.g. 2025/2026)
        $tahunIds = $tahunNamaBare
            ? TahunAkademik::where('nama', $tahunNamaBare)->pluck('id')
            : null;

        $totalAktif = RiwayatPendidikan::query()
            ->where('ro_status_siswa', 37)
            ->whereHas('jurusan', fn($q) => $q->where('nama', 'NOT LIKE', '%temp%'))
            ->when($tahunIds, fn(Builder $query) => $query->whereHas(
                'akademikKrs',
                fn($q) => $q->whereIn('id_tahun_akademik', $tahunIds)
            ))
            ->count();

        $totalPending = RiwayatPendidikan::query()
            ->whereIn('ro_status_siswa', [142, 43])
            ->whereHas('jurusan', fn($q) => $q->where('nama', 'NOT LIKE', '%temp%'))
            ->when($tahunIds, fn(Builder $query) => $query->whereIn('id_tahun_akademik', $tahunIds))
            ->count();

        // Hanya hitung calon pendaftar yang belum diterima (Status_Pendaftaran != 'Y')
        $totalPendaftar = SiswaDataPendaftar::query()
            ->where(function ($q) {
                $q->where('Status_Pendaftaran', '!=', 'Y')
                    ->orWhereNull('Status_Pendaftaran');
            })
            ->whereHas('jurusan', fn($q) => $q->where('nama', 'NOT LIKE', '%temp%'))
            ->when($tahunIds, fn(Builder $query) => $query->whereIn('id_tahun_akademik', $tahunIds))
            ->count();

        return [
            Stat::make('Total Mahasiswa Aktif', $totalAktif)
                ->description('Tahun ' . $tahunLabel)
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Pending / Non-Aktif', $totalPending)
                ->description('Status belum aktif, tahun ' . $tahunLabel)
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Calon Mahasiswa', $totalPendaftar)
                ->description('Belum diterima, tahun ' . $tahunLabel)
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }
}
