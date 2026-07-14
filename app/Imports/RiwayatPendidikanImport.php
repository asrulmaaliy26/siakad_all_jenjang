<?php

namespace App\Imports;

use App\Models\RiwayatPendidikan;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class RiwayatPendidikanImport implements OnEachRow, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public $successCount = 0;

    public function onRow(\Maatwebsite\Excel\Row $excelRow)
    {
        $row = $excelRow->toArray();

        // Skip empty rows (jika tidak ada data sama sekali)
        if (empty(array_filter($row))) {
            return;
        }

        // Resolving Jurusan
        $id_jurusan = $row['id_jurusan'] ?? null;
        if (!$id_jurusan) {
            $jurusan_name = $row['jurusan'] ?? $row['jurusan_nama'] ?? null;
            if ($jurusan_name) {
                $jurusan = \App\Models\Jurusan::where('nama', $jurusan_name)->first();
                if ($jurusan) $id_jurusan = $jurusan->id;
            }
        }

        // Resolving Siswa (jika kolom excel bernama 'nim' atau 'nomor_induk' dan id_siswa_data kosong)
        $id_siswa_data = $row['id_siswa_data'] ?? null;
        $nim = $row['nomor_induk'] ?? $row['nim'] ?? null;
        if (!$id_siswa_data && $nim) {
            $siswa = \App\Models\SiswaData::where('nomor_induk', $nim)->first();
            if ($siswa) {
                $id_siswa_data = $siswa->id;
            }
        }

        $id_riwayat = $row['id'] ?? $row['id_riwayat'] ?? null;

        // Jika tidak ada ID Riwayat, maka pastikan id_siswa_data dan id_jurusan terpenuhi untuk data baru
        if (!$id_riwayat && (!$id_siswa_data || !$id_jurusan)) {
            return;
        }

        // Siapkan array data update
        $updateData = [];
        if ($id_siswa_data) $updateData['id_siswa_data'] = $id_siswa_data;
        if ($id_jurusan) $updateData['id_jurusan'] = $id_jurusan;
        
        // Hanya update atribut yang ada di excel (tidak menimpa dengan null jika kolomnya tidak ada di excel)
        $fields = [
            'nomor_induk', 'ro_program_sekolah', 'ro_program_kelas', 'ro_status_siswa', 'id_wali_dosen',
            'tanggal_mulai', 'tanggal_selesai', 'foto_profil', 'mulai_smt', 'smt_aktif',
            'dosen_wali', 'no_seri_ijazah', 'sks_diakui', 'jalur_skripsi', 'judul_skripsi',
            'bln_awal_bimbingan', 'bln_akhir_bimbingan', 'sk_yudisium', 'tgl_sk_yudisium',
            'ipk', 'nm_pt_asal', 'nm_prodi_asal', 'ro_jns_daftar', 'ro_jns_keluar',
            'keluar_smt', 'keterangan', 'pembiayaan', 'status', 'id_tahun_akademik'
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $row)) {
                $updateData[$field] = $row[$field];
            }
        }
        
        // Kita tangkap relasi teks ke nilai ReferenceOption kalau perlu
        // Status Siswa (contoh dari TextColumn yang mengekspor 'statusSiswa.nilai')
        if (isset($row['status_siswa'])) {
            $ro = \App\Models\ReferenceOption::where('nama_grup', 'status_siswa')->where('nilai', $row['status_siswa'])->first();
            if ($ro) $updateData['ro_status_siswa'] = $ro->id;
        }
        // Program Kelas (contoh dari TextColumn yang mengekspor 'programKelas.nilai')
        if (isset($row['program_kelas'])) {
            $ro = \App\Models\ReferenceOption::where('nama_grup', 'program_kelas')->where('nilai', $row['program_kelas'])->first();
            if ($ro) $updateData['ro_program_kelas'] = $ro->id;
        }
        
        // Wali Dosen
        if (isset($row['wali_dosen'])) {
            $wd = \App\Models\DosenData::where('nama', $row['wali_dosen'])->first();
            if ($wd) $updateData['id_wali_dosen'] = $wd->id;
        }

        if ($id_riwayat) {
            RiwayatPendidikan::updateOrCreate(
                ['id' => $id_riwayat],
                $updateData
            );
        } else {
            RiwayatPendidikan::updateOrCreate(
                ['nomor_induk' => $nim],
                $updateData
            );
        }

        $this->successCount++;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
