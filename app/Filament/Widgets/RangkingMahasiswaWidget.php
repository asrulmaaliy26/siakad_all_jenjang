<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\RiwayatPendidikan;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;


class RangkingMahasiswaWidget extends Widget
{
    protected string $view = 'filament.widgets.rangking-mahasiswa-widget';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public $rankAngkatan = '-';
    public $rankKeseluruhan = '-';
    public $totalAngkatan = 0;
    public $totalKeseluruhan = 0;

    public static function canView(): bool
    {
        return auth()->user()->can('View:RangkingMahasiswaWidget');
    }

    protected function getColumns(): int | array
    {
        return 2;
    }

    public function mount()
    {
        $user = Auth::user();
        if (!$user || !$user->siswaData) {
            return;
        }

        $riwayatAktif = $user->siswaData->riwayatPendidikanAktif;
        if (!$riwayatAktif) {
            return;
        }

        // Tentukan SKS & IPK (diupdate dari AcademicStatsWidget atau diambil dari DB)
        $myIpk = $riwayatAktif->ipk ?? 0;

        // 1. Ranking Angkatan (Angkatan bisa dilihat dari id_tahun_akademik & id_jurusan)
        $angkatanQuery = RiwayatPendidikan::where('id_tahun_akademik', $riwayatAktif->id_tahun_akademik)
            ->where('id_jurusan', $riwayatAktif->id_jurusan)
            ->whereIn('status', ['Y', 'Aktif']);

        $this->totalAngkatan = $angkatanQuery->count();
        // Cari berapa orang di angkatan ini yang IPK-nya lebih tinggi dari mahasiswa ini
        $rankAngkatanPosition = $angkatanQuery->where('ipk', '>', $myIpk)->count() + 1;
        $this->rankAngkatan = $rankAngkatanPosition;

        // 2. Ranking Keseluruhan (Semua mahasiswa aktif di prodi yang sama atau prodi apa saja)
        // Menurut instruksi user: "seluruh mahasiswa". Biasanya ini seluruh mahasiswa aktif.
        $keseluruhanQuery = RiwayatPendidikan::whereIn('status', ['Y', 'Aktif']);

        $this->totalKeseluruhan = $keseluruhanQuery->count();
        // Berapa orang yang IPK-nya lebih tinggi
        $rankKeseluruhanPosition = $keseluruhanQuery->where('ipk', '>', $myIpk)->count() + 1;
        $this->rankKeseluruhan = $rankKeseluruhanPosition;
    }
}
