<?php

namespace App\Exports;

use App\Models\MataPelajaranKelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NilaiPddiktiExport implements FromCollection, WithHeadings
{
    protected $recordIds;

    public function __construct(array $recordIds = null)
    {
        $this->recordIds = $recordIds;
    }

    public function headings(): array
    {
        return [
            'nim',
            'kode_mata_kuliah',
            'semester',
            'nama_kelas',
            'nilai_angka',
            'nilai_huruf',
            'kode_prodi'
        ];
    }

    public function collection()
    {
        $query = MataPelajaranKelas::with([
            'mataPelajaranKurikulum.mataPelajaranMaster',
            'kelas.tahunAkademik',
            'kelas.jurusan',
            'siswaDataLjk.akademikKrs.riwayatPendidikan.siswa'
        ]);

        if ($this->recordIds !== null) {
            $query->whereIn('id', $this->recordIds);
        }

        $mataPelajaranKelas = $query->get();
        $exportData = collect();

        foreach ($mataPelajaranKelas as $mkKelas) {
            $kodeMataKuliah = $mkKelas->mataPelajaranKurikulum->mataPelajaranMaster->kode_feeder ?? '';
            $semester = $mkKelas->kelas->tahunAkademik->kode_pddikti ?? '';
            $namaKelas = $mkKelas->kelas->kode_pddikti ?? '';
            $kodeProdi = $mkKelas->kelas->jurusan->kode_prodi ?? '';

            foreach ($mkKelas->siswaDataLjk as $ljk) {
                // Ensure relations exist
                if (!$ljk->akademikKrs || !$ljk->akademikKrs->riwayatPendidikan) {
                    continue;
                }

                $nim = $ljk->akademikKrs->riwayatPendidikan->nomor_induk ?? '';
                $nilaiAngka = $ljk->Nilai_Akhir ?? 0;
                $nilaiHuruf = $ljk->Nilai_Huruf ?? 'E';

                $exportData->push([
                    'nim'              => $nim,
                    'kode_mata_kuliah' => $kodeMataKuliah,
                    'semester'         => $semester,
                    'nama_kelas'       => $namaKelas,
                    'nilai_angka'      => $nilaiAngka,
                    'nilai_huruf'      => $nilaiHuruf,
                    'kode_prodi'       => $kodeProdi,
                ]);
            }
        }

        return $exportData;
    }
}
