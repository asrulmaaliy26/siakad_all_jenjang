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
use App\Models\TaPengajuanJudul;
use App\Models\TaSeminarProposal;
use App\Models\TaSkripsi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UploadPathHelper
{
    public static function uploadPath($record, string $column, string $fallbackType = 'umum', callable $get = null)
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Tipe (Siswa / Dosen / Umum)
        $type = self::getType($record, $fallbackType);

        // 3. Column
        return "uploads/" . Str::slug($tahun) . "/" . Str::slug($type) . "/" . Str::slug($column);
    }

    public static function uploadDosenPath($dosen, string $table = 'dosen_dokumen', callable $get = null)
    {
        // Format: uploads/dosen/{Nama Dosen}/{Table}

        // 1. Role (Dosen)
        $role = 'dosen';

        // 2. Nama Dosen
        $namaDosen = 'Tanpa Nama';
        if ($dosen) {
            $namaDosen = $dosen->nama ?? 'Tanpa Nama';
        } elseif ($get) {
            $namaDosen = $get('nama') ?? 'Tanpa Nama';
        }

        return "uploads/" . Str::slug($role) . "/" . Str::slug($namaDosen) . "/" . Str::slug($table);
    }

    public static function uploadKrsPath($get, $record = null, string $table = 'akademik_krs')
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Type
        $typeFolder = 'mahasiswa';

        // 3. Nama Siswa/Mahasiswa
        $namaSiswa = self::getNamaSiswa($record, $get);

        // Format:
        // uploads/{Tahun}/{mahasiswa}/{Nama}/{Table}
        return "uploads/"
            . Str::slug($tahun) . "/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }

    public static function uploadMataPelajaranKelasPath($get, $record = null, string $table = 'soal_uts')
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Kelas Info (Program Kelas + Semester)
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

        // 3. Nama Mata Pelajaran
        $mapelNama = 'MapelUmum';
        if ($record && $record->mataPelajaranKurikulum && $record->mataPelajaranKurikulum->mataPelajaranMaster) {
            $mapelNama = $record->mataPelajaranKurikulum->mataPelajaranMaster->name ?? $record->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? 'MapelUmum';
        } elseif ($get && $mapelKurikulumId = $get('id_mata_pelajaran_kurikulum')) {
            $mk = \App\Models\MataPelajaranKurikulum::find($mapelKurikulumId);
            if ($mk && $mk->mataPelajaranMaster) {
                $mapelNama = $mk->mataPelajaranMaster->name ?? $mk->mataPelajaranMaster->nama ?? 'MapelUmum';
            }
        }

        return "uploads/" . Str::slug($tahun) . "/" . Str::slug($kelasInfo) . "/" . Str::slug($mapelNama) . "/" . Str::slug($table);
    }

    public static function uploadUjianPath($get, $record = null, string $table = 'ljk_uts')
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Type
        $typeFolder = 'mahasiswa';

        // 3. Nama Siswa
        $namaSiswa = self::getNamaSiswa($record, $get);

        // Format: uploads/{Tahun}/{mahasiswa}/{Nama Siswa}/{Table}
        return "uploads/"
            . Str::slug($tahun) . "/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }

    public static function uploadTugasPath($get, $record = null, string $taskIndex = '1')
    {
        // 1. Tahun Akademik
        $tahun = self::getYear($record, $get);

        // 2. Type
        $typeFolder = 'mahasiswa';

        // 3. Nama Siswa
        $namaSiswa = self::getNamaSiswa($record, $get);

        // Format: uploads/{Tahun}/{mahasiswa}/{Nama Siswa}/tugas_{index}
        return "uploads/"
            . Str::slug($tahun) . "/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($namaSiswa) . "/tugas_" . $taskIndex;
    }

    /**
     * Path upload untuk TA (Pengajuan Judul, Seminar Proposal, Skripsi).
     */
    public static function uploadTaPath($get, $record = null, string $table = 'ta'): string
    {
        // 1. Tahun Akademik
        $tahunAkademik = null;

        if ($record instanceof TaPengajuanJudul || $record instanceof TaSeminarProposal || $record instanceof TaSkripsi) {
            $tahunAkademik = $record->tahunAkademik;
        }

        if (!$tahunAkademik && $get) {
            $tahunId = $get('id_tahun_akademik');
            if ($tahunId) {
                $tahunAkademik = TahunAkademik::find($tahunId);
            }
        }

        if (!$tahunAkademik) {
            $tahunAkademik = self::fetchActiveYear();
        }

        $tahun = self::formatTahunAkademik($tahunAkademik);

        // 2. Nama Siswa
        $namaSiswa = 'tanpa-nama';

        if ($record instanceof TaPengajuanJudul || $record instanceof TaSeminarProposal || $record instanceof TaSkripsi) {
            $namaSiswa = $record->riwayatPendidikan?->siswa?->nama ?? 'tanpa-nama';
        }

        if (($namaSiswa === 'tanpa-nama') && $get) {
            $riwayatId = $get('id_riwayat_pendidikan');
            if ($riwayatId) {
                $riwayat = RiwayatPendidikan::find($riwayatId);
                $namaSiswa = $riwayat?->siswa?->nama ?? 'tanpa-nama';
            }
        }

        // 3. Susun path
        return "uploads/"
            . Str::slug($tahun) . "/"
            . "mahasiswa/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }

    public static function uploadSiswaDataPath($get, $record = null, string $table = 'foto_profil')
    {
        // 1. Type (mahasiswa)
        $typeFolder = 'mahasiswa';

        // 2. Tahun Angkatan
        $tahun = null;
        if ($record && $record instanceof SiswaData) {
            if ($record->riwayatPendidikanAktif) {
                $tahun = $record->riwayatPendidikanAktif->angkatan ?? null;
            }
            if (!$tahun && $record->created_at) {
                $tahun = $record->created_at->format('Y');
            }
        }
        $tahun = $tahun ?? date('Y');

        // 3. Nama Siswa
        $namaSiswa = 'Tanpa Nama';
        if ($record) {
            $namaSiswa = $record->nama ?? 'Tanpa Nama';
        } elseif ($get) {
            $namaSiswa = $get('nama') ?? 'Tanpa Nama';
        }

        // Format: uploads/mahasiswa/{Tahun}/{Nama Siswa}/{Table}
        return "uploads/"
            . Str::slug($typeFolder) . "/"
            . Str::slug($tahun) . "/"
            . Str::slug($namaSiswa) . "/"
            . Str::slug($table);
    }

    public static function uploadPendaftarPath($get, $record = null, string $table = 'Legalisir_Ijazah')
    {
        // 1. Type (mahasiswa)
        $typeFolder = 'mahasiswa';

        // 2. Tahun Akademik / Angkatan
        $tahun = null;
        if ($record && $record->tahunAkademik) {
            $tahun = substr($record->tahunAkademik->nama, 0, 4);
        } elseif ($get && $get('id_tahun_akademik')) {
            $tahunAkademik = \App\Models\TahunAkademik::find($get('id_tahun_akademik'));
            if ($tahunAkademik) {
                $tahun = substr($tahunAkademik->nama, 0, 4);
            }
        }

        if (!$tahun && $record && $record instanceof SiswaDataPendaftar && $record->siswaData) {
            $tahun = $record->siswaData->created_at->format('Y');
        } elseif (!$tahun) {
            $tahun = date('Y');
        }

        // 3. Nama Siswa
        $namaSiswa = 'Tanpa Nama';
        if ($record && $record instanceof SiswaDataPendaftar && $record->siswaData) {
            $namaSiswa = $record->siswaData->nama ?? 'Tanpa Nama';
        } elseif ($get) {
            if ($siswaId = $get('id_siswa_data')) {
                $siswa = SiswaData::find($siswaId);
                $namaSiswa = $siswa->nama ?? 'Tanpa Nama';
            }
        }

        // Format: uploads/mahasiswa/{Tahun}/{Nama Siswa}/{Table}
        return "uploads/"
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
            $tahunAkademik = $record->tahunAkademik ?? $record->kode_tahun;
        } elseif ($record instanceof MataPelajaranKelas) {
            $tahunAkademik = $record->kelas?->tahunAkademik;
        } elseif ($get) {
            if ($mpkId = $get('id_mata_pelajaran_kelas')) {
                $mpk = MataPelajaranKelas::find($mpkId);
                $tahunAkademik = $mpk?->kelas?->tahunAkademik;
            } elseif ($kelasId = $get('id_kelas')) {
                $kelas = Kelas::find($kelasId);
                $tahunAkademik = $kelas?->tahunAkademik;
            } elseif ($kodeTahun = $get('kode_tahun')) {
                $tahunAkademik = TahunAkademik::where('nama', $kodeTahun)->first() ?? $kodeTahun;
            }
        }

        if ($tahunAkademik) {
            return self::formatTahunAkademik($tahunAkademik);
        }

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
            return $tahunAkademik;
        }

        if ($tahunAkademik && is_object($tahunAkademik)) {
            $nama = $tahunAkademik->nama ?? '';
            $periode = $tahunAkademik->periode ?? '';

            if ($nama && $periode) {
                return $nama . '-' . $periode;
            } elseif ($nama) {
                return $nama;
            }
        }

        return date('Y');
    }

    protected static function getType($record, $fallback)
    {
        if ($record instanceof SiswaData || $record instanceof SiswaDataLJK || $record instanceof RiwayatPendidikan || $record instanceof AkademikKrs) {
            return 'mahasiswa';
        }

        if ($record instanceof DosenData || $record instanceof DosenDokumen || $record instanceof MataPelajaranKelas) {
            return 'dosen';
        }

        return $fallback;
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
}
