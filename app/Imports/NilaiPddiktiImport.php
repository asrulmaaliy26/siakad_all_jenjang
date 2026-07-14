<?php

namespace App\Imports;

use App\Models\SiswaData;
use App\Models\MataPelajaranMaster;
use App\Models\TahunAkademik;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\SiswaDataLJK;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NilaiPddiktiImport implements ToCollection, WithHeadingRow
{
    public $successCount = 0;
    public $failCount = 0;
    public $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Mapping Header Excel PDDIKTI ke array key (otomatis snake_case oleh Laravel Excel)
            $nim = $row['nim'] ?? null;
            $kodeMataKuliah = $row['kode_mata_kuliah'] ?? null;
            $semester = $row['semester'] ?? null; // e.g. 20211
            $namaKelas = $row['nama_kelas'] ?? null; // e.g. SI2A
            $nilaiAngka = $row['nilai_angka'] ?? 0;
            $nilaiHuruf = $row['nilai_huruf'] ?? 'E';
            $kodeProdi = $row['kode_prodi'] ?? null;

            if (!$nim || !$kodeMataKuliah || !$semester || !$kodeProdi) {
                $this->failCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": Data krusial (NIM, Kode MK, Semester, Kode Prodi) tidak lengkap.";
                continue;
            }

            // 1. Cari Siswa
            $siswa = SiswaData::where('nomor_induk', $nim)->first();
            if (!$siswa) {
                $this->failCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": Siswa dengan NIM {$nim} tidak ditemukan.";
                continue;
            }

            // 2. Cari Mata Pelajaran Master
            $mkMaster = MataPelajaranMaster::where('kode_feeder', $kodeMataKuliah)->first();
            if (!$mkMaster) {
                $this->failCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": Mata Pelajaran dengan Kode {$kodeMataKuliah} tidak ditemukan.";
                continue;
            }

            // 3. Cari Tahun Akademik
            $tahunAkademik = TahunAkademik::where('kode_pddikti', $semester)->first();
            if (!$tahunAkademik) {
                $this->failCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": Tahun Akademik dengan Kode Semester {$semester} tidak ditemukan.";
                continue;
            }

            // 4. Cari Jurusan
            $jurusan = Jurusan::where('kode_prodi', $kodeProdi)->first();
            if (!$jurusan) {
                $this->failCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": Jurusan dengan Kode Prodi {$kodeProdi} tidak ditemukan.";
                continue;
            }

            // 5. Cari Kelas (berdasarkan kode_pddikti dan jurusan serta tahun akademik)
            // Asumsi: kelas berada di jurusan & tahun akademik yang sama
            $kelasQuery = Kelas::where('kode_pddikti', $namaKelas);
            if ($namaKelas) {
                $kelas = $kelasQuery->first();
            } else {
                $kelas = null;
            }

            // 6. Cari MataPelajaranKelas
            $mkKelasQuery = MataPelajaranKelas::whereHas('mataPelajaranKurikulum', function($q) use ($mkMaster) {
                $q->where('id_mata_pelajaran_master', $mkMaster->id);
            });

            if ($kelas) {
                $mkKelasQuery->where('id_kelas', $kelas->id);
            }
            
            $mkKelas = $mkKelasQuery->first();

            if (!$mkKelas) {
                $this->failCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": Mata Pelajaran Kelas tidak ditemukan (MK: {$kodeMataKuliah}, Kelas: {$namaKelas}).";
                continue;
            }

            // 7. Cari LJK (Lembar Nilai) terkait
            // LJK relasinya dari AkademikKrs, yang dimiliki oleh Riwayat Pendidikan siswa.
            // Kita cari LJK yang sesuai dengan id_mata_pelajaran_kelas dan milik siswa ini.
            $ljk = SiswaDataLJK::where('id_mata_pelajaran_kelas', $mkKelas->id)
                ->whereHas('akademikKrs.riwayatPendidikan', function($q) use ($siswa) {
                    $q->where('id_siswa_data', $siswa->id);
                })
                ->first();

            if (!$ljk) {
                $this->failCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": KRS/LJK untuk Siswa NIM {$nim} pada kelas tersebut tidak ditemukan.";
                continue;
            }

            // 8. Update Nilai (Bypass Model Event)
            // Karena event boot di model akan overwrite nilai akhir, kita bypass dengan update langsung.
            $statusNilai = (floatval($nilaiAngka) >= 55) ? 'LULUS' : 'TL';
            
            // Kita update array langsung atau menggunakan updateQuietly() jika model support
            $ljk->Nilai_Akhir = round(floatval($nilaiAngka), 2);
            $ljk->Nilai_Huruf = $nilaiHuruf;
            $ljk->Status_Nilai = $statusNilai;
            $ljk->saveQuietly(); // Bypass events in Laravel 8+

            $this->successCount++;
        }
    }
}
