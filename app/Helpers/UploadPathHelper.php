<?php

namespace App\Helpers;

use App\Models\TahunAkademik;
use App\Models\SiswaData;
use App\Models\DosenData;
use App\Models\DosenDokumen;
use App\Models\SiswaDataLJK;
use App\Models\RiwayatPendidikan;
use App\Models\AkademikKrs;
use App\Models\MataPelajaranKelas;
use App\Models\Kelas;
use App\Models\SiswaDataPendaftar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UploadPathHelper
{
    public static function uploadPath($record, string $column, string $fallbackType = 'umum', callable $get = null)
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Jenjang Pendidikan
        $jenjang = self::getJenjang($record, $get);

        // 3. Tipe (Siswa / Dosen / Umum)
        $type = self::getType($record, $fallbackType);

        // 4. Column
        return "uploads/" . Str::slug($tahun) . "/" . Str::slug($jenjang) . "/" . Str::slug($type) . "/" . Str::slug($column);
    }

    public static function uploadDosenPath($dosen, string $table = 'dosen_dokumen', callable $get = null)
    {
        // Format: uploads/{Jenjang}/{Dosen|Guru}/{Nama Dosen}/{Table}

        // 1. Jenjang Info
        $jenjangData = self::getJenjangDataForDosen($dosen, $get);
        $jenjangNama = $jenjangData['nama'] ?? 'Umum';
        $jenjangType = strtolower($jenjangData['type'] ?? 'sekolah');

        // 2. Role (Dosen/Guru)
        $role = ($jenjangType === 'kampus') ? 'dosen' : 'guru';

        // 3. Nama Dosen
        $namaDosen = 'Tanpa Nama';
        if ($dosen) {
            $namaDosen = $dosen->nama ?? 'Tanpa Nama';
        } elseif ($get) {
            $namaDosen = $get('nama') ?? 'Tanpa Nama';
        }

        return "uploads/" . Str::slug($jenjangNama) . "/" . Str::slug($role) . "/" . Str::slug($namaDosen) . "/" . Str::slug($table);
    }

    public static function uploadKrsPath($get, $record = null, string $table = 'akademik_krs')
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Jenjang Pendidikan
        $jenjangData = self::getJenjangData($record, $get);
        $jenjangNama = $jenjangData['nama'] ?? 'Umum';

        // 3. Tentukan type berdasarkan jenjang
        $jenjangType = strtolower($jenjangData['type'] ?? 'sekolah');

        $typeFolder = ($jenjangType === 'kampus') ? 'mahasiswa' : 'siswa';

        // 4. Nama Siswa/Mahasiswa
        $namaSiswa = self::getNamaSiswa($record, $get);

        // Format:
        // uploads/{Tahun}/{Jenjang}/{siswa|mahasiswa}/{Nama}/{Table}
        return "uploads/"
            . Str::slug($tahun) . "/"
            . Str::slug($jenjangNama) . "/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }

    public static function uploadMataPelajaranKelasPath($get, $record = null, string $table = 'soal_uts')
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Jenjang Pendidikan
        $jenjang = self::getJenjang($record, $get);

        // 3. Kelas Info (Program Kelas + Semester)
        $kelasInfo = 'KelasUmum';
        if ($record && $record->kelas) {
            $prog = $record->kelas->programKelas->nilai ?? '';
            $sem = $record->kelas->semester ?? '';
            $kelasInfo = "kelas {$prog}{$sem}";
        } elseif ($get && $kelasId = $get('id_kelas')) {
            $kelas = Kelas::find($kelasId);
            if ($kelas) {
                $prog = $kelas->programKelas->nilai ?? '';
                $sem = $kelas->semester ?? '';
                $kelasInfo = "kelas {$prog}{$sem}";
            }
        }

        // 4. Nama Mata Pelajaran
        $mapelNama = 'MapelUmum';
        if ($record && $record->mataPelajaranKurikulum && $record->mataPelajaranKurikulum->mataPelajaranMaster) {
            $mapelNama = $record->mataPelajaranKurikulum->mataPelajaranMaster->name ?? $record->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? 'MapelUmum';
        } elseif ($get && $mapelKurikulumId = $get('id_mata_pelajaran_kurikulum')) {
            $mk = \App\Models\MataPelajaranKurikulum::find($mapelKurikulumId);
            if ($mk && $mk->mataPelajaranMaster) {
                $mapelNama = $mk->mataPelajaranMaster->name ?? $mk->mataPelajaranMaster->nama ?? 'MapelUmum';
            }
        }

        return "uploads/" . Str::slug($tahun) . "/" . Str::slug($jenjang) . "/" . Str::slug($kelasInfo) . "/" . Str::slug($mapelNama) . "/" . Str::slug($table);
    }

    public static function uploadUjianPath($get, $record = null, string $table = 'ljk_uts')
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Jenjang Pendidikan
        $jenjangData = self::getJenjangData($record, $get);
        $jenjangNama = $jenjangData['nama'] ?? 'Umum';

        // 3. Tentukan type berdasarkan jenjang
        $jenjangType = strtolower($jenjangData['type'] ?? 'sekolah');
        $typeFolder = ($jenjangType === 'kampus') ? 'mahasiswa' : 'siswa';

        // 4. Nama Siswa
        $namaSiswa = self::getNamaSiswa($record, $get);

        // Format: uploads/{Tahun}/{Jenjang}/{siswa/mahasiswa}/{Nama Siswa}/{Table}
        return "uploads/"
            . Str::slug($tahun) . "/"
            . Str::slug($jenjangNama) . "/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }



    public static function uploadSiswaDataPath($get, $record = null, string $table = 'foto_profil')
    {
        // 1. Jenjang Pendidikan
        // Cek Riwayat Pendidikan
        $jenjang = null;
        if ($record && $record instanceof SiswaData) {
            if ($record->riwayatPendidikanAktif && $record->riwayatPendidikanAktif->jurusan) {
                $jenjang = $record->riwayatPendidikanAktif->jurusan->jenjangPendidikan;
            }
            // Jika null, cek Pendaftar
            if (!$jenjang && $record->pendaftar && $record->pendaftar->jurusan) {
                $jenjang = $record->pendaftar->jurusan->jenjangPendidikan;
            }
        }

        $jenjangNama = $jenjang->nama ?? 'Umum';

        // 2. Type (siswa/mahasiswa)
        $jenjangType = strtolower($jenjang->type ?? 'sekolah');
        $typeFolder = ($jenjangType === 'kampus') ? 'mahasiswa' : 'siswa';

        // 3. Tahun Angkatan
        $tahun = null;
        if ($record && $record instanceof SiswaData) {
            if ($record->riwayatPendidikanAktif) {
                // Assuming 'angkatan' field exists or is derived. If not, fallback to created_at
                $tahun = $record->riwayatPendidikanAktif->angkatan ?? null;
            }
            // Jika null, ambil tahun created_at
            if (!$tahun && $record->created_at) {
                $tahun = $record->created_at->format('Y');
            }
        }
        $tahun = $tahun ?? date('Y');

        // 4. Nama Siswa
        $namaSiswa = 'Tanpa Nama';
        if ($record) {
            $namaSiswa = $record->nama ?? 'Tanpa Nama';
        } elseif ($get) {
            $namaSiswa = $get('nama') ?? 'Tanpa Nama';
        }

        // Format: uploads/{Jenjang}/{siswa/mahasiswa}/{Tahun}/{Nama Siswa}/{Table}
        // Request: /S1/mahasiswa/2025/elma/foto_profil
        return "uploads/"
            . Str::slug($jenjangNama) . "/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($tahun) . "/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }

    public static function uploadPendaftarPath($get, $record = null, string $table = 'Legalisir_Ijazah')
    {
        // 1. Jenjang Pendidikan
        $jenjang = null;
        if ($record && $record instanceof SiswaDataPendaftar) {
            $jenjang = $record->jurusan?->jenjangPendidikan;
        } elseif ($get) {
            if ($jurusanId = $get('id_jurusan')) {
                $jurusan = \App\Models\Jurusan::find($jurusanId);
                $jenjang = $jurusan?->jenjangPendidikan;
            }
        }

        $jenjangNama = $jenjang->nama ?? 'Umum';

        // 2. Type (siswa/mahasiswa)
        $jenjangType = strtolower($jenjang->type ?? 'sekolah');
        $typeFolder = ($jenjangType === 'kampus') ? 'mahasiswa' : 'siswa';

        // 3. Tahun Masuk / Angkatan
        $tahun = null;
        if ($record) {
            $tahun = $record->Tahun_Masuk;
        } elseif ($get) {
            $tahun = $get('Tahun_Masuk');
        }

        if (!$tahun && $record && $record instanceof SiswaDataPendaftar && $record->siswaData) {
            $tahun = $record->siswaData->created_at->format('Y');
        } elseif (!$tahun) {
            $tahun = date('Y');
        }

        // 4. Nama Siswa
        $namaSiswa = 'Tanpa Nama';
        if ($record && $record instanceof SiswaDataPendaftar && $record->siswaData) {
            $namaSiswa = $record->siswaData->nama ?? 'Tanpa Nama';
        } elseif ($get) {
            if ($siswaId = $get('id_siswa_data')) {
                $siswa = SiswaData::find($siswaId);
                $namaSiswa = $siswa->nama ?? 'Tanpa Nama';
            }
        }

        // Format: uploads/{Jenjang}/{siswa/mahasiswa}/{Tahun}/{Nama Siswa}/{Table}
        return "uploads/"
            . Str::slug($jenjangNama) . "/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($tahun) . "/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }

    protected static function getYear($record, $get = null)
    {
        $tahunAkademik = null;

        if ($record instanceof SiswaDataLJK) {
            $tahunAkademik = $record->mataPelajaranKelas?->kelas?->tahunAkademik;
        } elseif ($record instanceof AkademikKrs) {
            $tahunAkademik = $record->kelas?->tahunAkademik;
        } elseif ($record instanceof MataPelajaranKelas) {
            $tahunAkademik = $record->kelas?->tahunAkademik;
        } elseif ($record instanceof SiswaData) {
            // Untuk SiswaData, logika tahun ditangani khusus di uploadSiswaDataPath
            $tahunAkademik = null;
        } elseif ($get) {
            if ($mpkId = $get('id_mata_pelajaran_kelas')) {
                $mpk = MataPelajaranKelas::find($mpkId);
                $tahunAkademik = $mpk?->kelas?->tahunAkademik;
            } elseif ($kelasId = $get('id_kelas')) {
                $kelas = Kelas::find($kelasId);
                $tahunAkademik = $kelas?->tahunAkademik;
            }
        }

        // Jika tahun akademik ditemukan, format dengan periode
        if ($tahunAkademik) {
            return self::formatTahunAkademik($tahunAkademik);
        }

        // Fallback ke tahun akademik aktif
        $activeYear = self::fetchActiveYear();
        return $activeYear ? self::formatTahunAkademik($activeYear) : date('Y');
    }

    protected static function fetchActiveYear()
    {
        return TahunAkademik::where('status', 'Aktif')->first();
    }

    protected static function formatTahunAkademik($tahunAkademik)
    {
        if (is_string($tahunAkademik)) {
            // Jika sudah berupa string, coba parsing
            return $tahunAkademik;
        }

        if ($tahunAkademik && is_object($tahunAkademik)) {
            $nama = $tahunAkademik->nama ?? '';
            $periode = $tahunAkademik->periode ?? '';

            // Format: 2025-2026-Ganjil
            if ($nama && $periode) {
                return $nama . '-' . $periode;
            } elseif ($nama) {
                return $nama;
            }
        }

        return date('Y');
    }

    protected static function getJenjang($record, $get = null)
    {
        if ($record instanceof SiswaDataLJK) {
            return $record->akademikKrs?->riwayatPendidikan?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
        }

        if ($record instanceof MataPelajaranKelas) {
            return $record->kelas?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
        }

        if ($record instanceof SiswaData) {
            return $record->riwayatPendidikanAktif?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
        }

        if ($record instanceof AkademikKrs) {
            return $record->riwayatPendidikan?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
        }

        if ($record instanceof RiwayatPendidikan) {
            return $record->jurusan?->jenjangPendidikan?->nama ?? 'umum';
        }

        if ($record instanceof SiswaDataPendaftar) {
            return $record->jurusan?->jenjangPendidikan?->nama ?? 'umum';
        }

        // Try using $get if record is null
        if ($get) {
            if ($riwayatId = $get('id_riwayat_pendidikan')) {
                $riwayat = RiwayatPendidikan::find($riwayatId);
                return $riwayat?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
            }
            if ($jurusanId = $get('id_jurusan')) {
                $jurusan = \App\Models\Jurusan::find($jurusanId);
                return $jurusan?->jenjangPendidikan?->nama ?? 'umum';
            }
            if ($mpkId = $get('id_mata_pelajaran_kelas')) {
                $mpk = MataPelajaranKelas::find($mpkId);
                return $mpk?->kelas?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
            }
            if ($siswaId = $get('id_siswa_data')) {
                $siswa = SiswaData::find($siswaId);
                return $siswa?->riwayatPendidikanAktif?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
            }
            if ($krsId = $get('id_akademik_krs')) {
                $krs = AkademikKrs::find($krsId);
                // krs -> riwayat -> jurusan -> jenjang
                return $krs?->riwayatPendidikan?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
            }
            if ($kelasId = $get('id_kelas')) {
                $kelas = Kelas::find($kelasId);
                return $kelas?->jurusan?->jenjangPendidikan?->nama ?? 'umum';
            }
        }

        return 'umum';
    }

    protected static function getType($record, $fallback)
    {
        if ($record instanceof SiswaData || $record instanceof SiswaDataLJK || $record instanceof RiwayatPendidikan || $record instanceof AkademikKrs) {
            return 'siswa';
        }

        if ($record instanceof DosenData || $record instanceof DosenDokumen || $record instanceof MataPelajaranKelas) {
            return 'dosen';
        }

        return $fallback;
    }

    protected static function getJenjangData($record, $get)
    {
        $jenjang = null;
        if ($record instanceof AkademikKrs) {
            $jenjang = $record->riwayatPendidikan?->jurusan?->jenjangPendidikan;
        } elseif ($record instanceof SiswaDataLJK) {
            $jenjang = $record->akademikKrs?->riwayatPendidikan?->jurusan?->jenjangPendidikan;
        } elseif ($record instanceof SiswaData) {
            // Prioritas 1: Riwayat Pendidikan Aktif
            if ($record->riwayatPendidikanAktif && $record->riwayatPendidikanAktif->jurusan) {
                $jenjang = $record->riwayatPendidikanAktif->jurusan->jenjangPendidikan;
            }
            // Prioritas 2: Pendaftar
            if (!$jenjang && $record->pendaftar && $record->pendaftar->jurusan) {
                $jenjang = $record->pendaftar->jurusan->jenjangPendidikan;
            }
        } elseif ($record instanceof SiswaDataPendaftar) {
            $jenjang = $record->jurusan?->jenjangPendidikan;
        } elseif ($get) {
            if ($riwayatId = $get('id_riwayat_pendidikan')) {
                $riwayat = RiwayatPendidikan::find($riwayatId);
                $jenjang = $riwayat?->jurusan?->jenjangPendidikan;
            }
        }

        return [
            'nama' => $jenjang?->nama ?? 'Umum',
            'type' => $jenjang?->type ?? 'siswa',
        ];
    }

    protected static function getNamaSiswa($record, $get)
    {
        if ($record instanceof AkademikKrs) {
            return $record->riwayatPendidikan?->siswaData?->nama ?? 'Tanpa Nama';
        } elseif ($record instanceof SiswaDataLJK) {
            return $record->akademikKrs?->riwayatPendidikan?->siswaData?->nama ?? 'Tanpa Nama';
        } elseif ($record instanceof SiswaDataPendaftar) {
            return $record->siswaData?->nama ?? 'Tanpa Nama';
        } elseif ($get) {
            if ($riwayatId = $get('id_riwayat_pendidikan')) {
                $riwayat = RiwayatPendidikan::find($riwayatId);
                return $riwayat?->siswaData?->nama ?? 'Tanpa Nama';
            }
        }
        return 'Tanpa Nama';
    }

    protected static function getJenjangDataForDosen($record, $get)
    {
        $jenjang = null;
        if ($record && $record->jurusan && $record->jurusan->jenjangPendidikan) {
            $jenjang = $record->jurusan->jenjangPendidikan;
        } elseif ($get) {
            if ($jurusanId = $get('id_jurusan')) {
                $jurusan = \App\Models\Jurusan::find($jurusanId);
                $jenjang = $jurusan?->jenjangPendidikan;
            }
        }

        return [
            'nama' => $jenjang?->nama ?? 'Umum',
            'type' => $jenjang?->type ?? 'sekolah',
        ];
    }
}
