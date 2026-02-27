<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\SiswaDataLJK;
use App\Models\AkademikKrs;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class AcademicStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('View:AcademicStatsWidget');
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        if (!$user || !$user->siswaData) {
            return [];
        }

        $riwayatAktif = $user->siswaData->riwayatPendidikanAktif;
        if (!$riwayatAktif) {
            return [
                Stat::make('IPK Total', '0.00')->description('Belum ada data akademik')->color('gray'),
                Stat::make('IP Semester Ini', '0.00')->color('gray'),
                Stat::make('Total SKS Ditempuh', '0')->color('gray'),
            ];
        }

        // Ambil semua LJK untuk mahasiswa ini beserta SKS
        $allLjk = SiswaDataLJK::whereHas('akademikKrs', function ($q) use ($riwayatAktif) {
            $q->where('id_riwayat_pendidikan', $riwayatAktif->id);
        })->with([
            'akademikKrs',
            'mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster'
        ])->get();

        $totalSumbotTotal = 0;
        $totalSksTotal = 0;

        // Group by Semester via KRS
        $semesterTerakhir = 0;
        $ljkPerSemester = [];

        foreach ($allLjk as $ljk) {
            // Dapatkan SKS
            $sks = $ljk->mataPelajaranKelas->mataPelajaranKurikulum->mataPelajaranMaster->bobot ?? 0;
            $bobotNilai = $ljk->bobot; // Dari accessor getBobotAttribute()

            $krs = $ljk->akademikKrs;
            $semester = $krs ? $krs->semester : 0;
            if ($semester > $semesterTerakhir) {
                $semesterTerakhir = $semester;
            }

            if (!isset($ljkPerSemester[$semester])) {
                $ljkPerSemester[$semester] = ['sumbot' => 0, 'total_sks' => 0];
            }

            // Hitung untuk Total IPK
            if ($bobotNilai > 0) {
                // Hanya matkul yang lulus atau sudah diisi nilainya
                $totalSksTotal += $sks;
                $totalSumbotTotal += ($bobotNilai * $sks);
            }

            // Hitung per semester
            $ljkPerSemester[$semester]['total_sks'] += $sks;
            $ljkPerSemester[$semester]['sumbot'] += ($bobotNilai * $sks);
        }

        // Kalkulasi IPK Total
        $ipk = $totalSksTotal > 0 ? round($totalSumbotTotal / $totalSksTotal, 2) : 0;

        // Update attribute ipk di RiwayatPendidikan
        if ($riwayatAktif->ipk != $ipk) {
            $riwayatAktif->updateQuietly(['ipk' => $ipk]);
        }

        $stats = [];

        // Sort semester keys
        ksort($ljkPerSemester);

        // Jika belum ada semester sama sekali, tampilkan default 1
        if (empty($ljkPerSemester) || (count($ljkPerSemester) === 1 && isset($ljkPerSemester[0]))) {
            $stats[] = Stat::make('IP Semester 1', '0.00')
                ->description('Belum ada nilai')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('gray');
        } else {
            // Tampilkan untuk tiap semester
            foreach ($ljkPerSemester as $smt => $data) {
                if ($smt == 0) continue; // Skip jika semester 0/tidak terdefinisi secara valid

                $sSks = $data['total_sks'];
                $sSumbot = $data['sumbot'];
                $ips = $sSks > 0 ? round($sSumbot / $sSks, 2) : 0;

                $stats[] = Stat::make('IP Semester ' . $smt, number_format($ips, 2))
                    ->description('Indeks Prestasi Semester ' . $smt)
                    ->descriptionIcon('heroicon-m-chart-bar')
                    ->color($ips >= 3.0 ? 'success' : 'warning');
            }
        }

        // Tambahkan IPK dan Total SKS di akhir
        $stats[] = Stat::make('IPK Total', number_format($ipk, 2))
            ->description('Indeks Prestasi Kumulatif')
            ->descriptionIcon('heroicon-m-academic-cap')
            ->color($ipk >= 3.0 ? 'success' : 'warning');

        $stats[] = Stat::make('Total SKS Ditempuh', $totalSksTotal)
            ->description('Jumlah SKS yang lulus/dinilai')
            ->descriptionIcon('heroicon-m-book-open')
            ->color('primary');

        return $stats;
    }
}
