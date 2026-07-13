<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapNilaiMahasiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        // Get all LJK data based on the current table query (which includes filters)
        $ljks = $this->query->with([
            'akademikKrs.riwayatPendidikan.siswa',
            'akademikKrs.riwayatPendidikan.programKelas',
            'mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster'
        ])->get();

        // Group by Student (using id_riwayat_pendidikan)
        $grouped = $ljks->groupBy(function ($ljk) {
            return $ljk->akademikKrs->id_riwayat_pendidikan;
        });

        $exportData = collect();
        $no = 1;

        foreach ($grouped as $idRiwayat => $studentLjks) {
            $firstLjk = $studentLjks->first();
            $riwayat = $firstLjk->akademikKrs->riwayatPendidikan;
            $siswa = $riwayat->siswa;

            $totalSks = 0;
            $totalBobot = 0;

            foreach ($studentLjks as $ljk) {
                $sks = $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
                $bobot = $ljk->bobot; // This is Nilai_Akhir
                
                $totalSks += $sks;
                $totalBobot += ($bobot * $sks);
            }

            $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

            $exportData->push([
                'no' => $no++,
                'nim' => $riwayat->nomor_induk,
                'nama' => $siswa->nama_lengkap ?? $siswa->nama,
                'program_kelas' => $riwayat->programKelas->nilai ?? '-',
                'total_sks' => $totalSks,
                'ipk' => $ipk,
            ]);
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama Mahasiswa',
            'Program Kelas',
            'Total SKS',
            'Rata-rata Nilai Akhir',
        ];
    }

    public function map($row): array
    {
        return [
            $row['no'],
            $row['nim'],
            $row['nama'],
            $row['program_kelas'],
            $row['total_sks'],
            $row['ipk'],
        ];
    }
}
