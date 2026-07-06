<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use Filament\Widgets\Widget;
use App\Filament\Resources\SiswaDataPendaftars\Pages\ListSiswaDataPendaftars;

class PendaftarOverviewStats extends Widget
{
    #[\Livewire\Attributes\Url]
    public ?string $activeTab = null;

    #[\Livewire\Attributes\Url]
    public ?array $tableFilters = null;

    protected string $view = 'filament.widgets.pendaftar-overview-stats';

    // We want this widget to span full width
    protected int | string | array $columnSpan = 'full';

    protected function getTablePage(): string
    {
        return ListSiswaDataPendaftars::class;
    }

    protected function getViewData(): array
    {
        $baseQuery = \App\Models\SiswaDataPendaftar::query();

        // Apply Tab Filter manually to match the Page table
        if ($this->activeTab === 'belum_validasi') {
            $baseQuery->where('status_valid', '0');
        } elseif ($this->activeTab === 'sudah_validasi') {
            $baseQuery->where('status_valid', '1');
        } elseif ($this->activeTab === 'proses') {
            $baseQuery->where('Status_Pendaftaran', 'B');
        } elseif ($this->activeTab === 'lulus') {
            $baseQuery->where('Status_Pendaftaran', 'Y');
        } elseif ($this->activeTab === 'tidak_lulus') {
            $baseQuery->where('Status_Pendaftaran', 'N');
        }

        // Apply Akademik Year filter if selected
        $tahunAkademikFilter = $this->tableFilters['id_tahun_akademik']['values'] ?? null;
        if (!empty($tahunAkademikFilter)) {
            $baseQuery->whereIn('id_tahun_akademik', $tahunAkademikFilter);
        }

        $pendaftars = $baseQuery->toBase()
            ->select('id_jurusan', 'id_tahun_akademik', 'Status_Pendaftaran')
            ->selectRaw('count(*) as total')
            ->groupBy('id_jurusan', 'id_tahun_akademik', 'Status_Pendaftaran')
            ->get();

        $jurusanIds = $pendaftars->pluck('id_jurusan')->unique()->filter();
        $jurusans = \App\Models\Jurusan::whereIn('id', $jurusanIds)
            ->where('nama', 'NOT LIKE', '%Temp%')
            ->get()->keyBy('id');

        $tahunIds = $pendaftars->pluck('id_tahun_akademik')->unique()->filter();
        $tahuns = \App\Models\TahunAkademik::whereIn('id', $tahunIds)->get()->keyBy('id');

        $prodiStatsRaw = [];
        $angkatanStatsRaw = [];

        foreach ($pendaftars as $row) {
            // -- Prodi Logic --
            $jurusan = $jurusans->get($row->id_jurusan);
            if ($jurusan) {
                $nama = $jurusan->nama;

                // Check jenjang
                $jenjang = 'S1';
                if (stripos($nama, 'S2') !== false || stripos($nama, 'Magister') !== false) {
                    $jenjang = 'S2';
                }

                // Generate abbreviation
                $prodi = '';
                $namaLower = strtolower($nama);
                if (str_contains($namaLower, 'hukum keluarga islam')) {
                    $prodi = 'HKI';
                } elseif (str_contains($namaLower, 'ekonomi syariah')) {
                    $prodi = 'ES';
                } elseif (str_contains($namaLower, 'pendidikan bahasa arab') && $jenjang === 'S1') {
                    $prodi = 'PBA';
                } elseif (str_contains($namaLower, 'pendidikan bahasa arab') && $jenjang === 'S2') {
                    $prodi = 'S2-PBA';
                } elseif (str_contains($namaLower, 'manajemen pendidikan islam')) {
                    $prodi = 'MPI';
                } elseif (str_contains($namaLower, 'pendidikan guru madrasah')) {
                    $prodi = 'PGMI';
                } elseif (str_contains($namaLower, 'al-qur\'an dan tafsir') || str_contains($namaLower, 'al quran dan tafsir')) {
                    $prodi = 'IAT';
                } elseif (str_contains($namaLower, 'ilmu hadis')) {
                    $prodi = 'ILHA';
                } elseif (str_contains($namaLower, 'studi islam')) {
                    $prodi = 'SI';
                } else {
                    $words = explode(' ', str_ireplace(['S2', 'Magister'], '', $nama));
                    foreach ($words as $word) {
                        $word = trim($word);
                        if (!empty($word) && ctype_alpha($word[0]) && strtolower($word) !== 'dan') {
                            $prodi .= strtoupper($word[0]);
                        }
                    }
                    if ($jenjang === 'S2') {
                        $prodi = 'S2-' . $prodi;
                    }
                }

                $key = $jurusan->id;
                if (!isset($prodiStatsRaw[$key])) {
                    $prodiStatsRaw[$key] = [
                        'id' => $jurusan->id,
                        'jenjang' => $jenjang,
                        'prodi' => $prodi,
                        'selesai' => 0,
                        'proses' => 0,
                        'jumlah' => 0,
                    ];
                }

                if ($row->Status_Pendaftaran == 'B') {
                    $prodiStatsRaw[$key]['proses'] += $row->total;
                } else {
                    $prodiStatsRaw[$key]['selesai'] += $row->total;
                }
                $prodiStatsRaw[$key]['jumlah'] += $row->total;
            }

            // -- Angkatan Logic --
            $tahunKey = $row->id_tahun_akademik ?: 0;
            $tahunObj = $tahuns->get($row->id_tahun_akademik);
            $namaTahun = $tahunObj ? $tahunObj->nama : 'Tidak Diketahui';

            if (!isset($angkatanStatsRaw[$tahunKey])) {
                $angkatanStatsRaw[$tahunKey] = [
                    'id' => $tahunKey,
                    'angkatan' => $namaTahun,
                    'selesai' => 0,
                    'proses' => 0,
                    'jumlah' => 0,
                ];
            }

            if ($row->Status_Pendaftaran == 'B') {
                $angkatanStatsRaw[$tahunKey]['proses'] += $row->total;
            } else {
                $angkatanStatsRaw[$tahunKey]['selesai'] += $row->total;
            }
            $angkatanStatsRaw[$tahunKey]['jumlah'] += $row->total;
        }

        $prodiStats = collect(array_values($prodiStatsRaw))->sortBy([
            ['jenjang', 'asc'],
            ['jumlah', 'desc']
        ])->values();

        $jenjangStats = $prodiStats->groupBy('jenjang')->map(function ($group, $jenjang) {
            return [
                'jenjang' => $jenjang,
                'selesai' => $group->sum('selesai'),
                'proses' => $group->sum('proses'),
                'jumlah' => $group->sum('jumlah'),
            ];
        })->values();

        $angkatanStats = collect(array_values($angkatanStatsRaw))->sortByDesc('angkatan')->values();

        return [
            'prodiStats' => $prodiStats,
            'jenjangStats' => $jenjangStats,
            'angkatanStats' => $angkatanStats,
        ];
    }
}
