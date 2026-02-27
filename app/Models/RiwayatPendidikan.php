<?php

namespace App\Models;

use App\Models\RefOption\StatusSiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SiswaData;
use App\Models\Jurusan;
use App\Models\AkademikKrs;
use App\Models\RefOption\ProgramSekolah;
use App\Models\RefOption\JenisPendaftaran;
use App\Models\RefOption\JenisKeluar;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pendidikan';

    protected static function booted()
    {
        static::deleting(function ($riwayat) {
            // Delete related KRS (this will trigger AkademikKrs deleting event)
            $riwayat->akademikKrs->each->delete();

            // Delete related Title Submissions
            $riwayat->pengajuanJudul()->delete();

            // Delete related Proposals
            $riwayat->seminarProposal()->delete();

            // Delete related Thesis/Skripsi
            $riwayat->skripsi()->delete();
        });
    }

    protected $fillable = [
        'id_siswa_data',
        // 'id_jenjang_pendidikan', // Derived from Jurusan
        'id_jurusan',
        'ro_program_sekolah',
        'nomor_induk',
        'ro_status_siswa',
        'id_tahun_akademik',
        'tanggal_mulai',
        'tanggal_selesai',
        'foto_profil',
        'mulai_smt',
        'smt_aktif',
        'dosen_wali',
        'no_seri_ijazah',
        'sks_diakui',
        'jalur_skripsi',
        'judul_skripsi',
        'bln_awal_bimbingan',
        'bln_akhir_bimbingan',
        'sk_yudisium',
        'tgl_sk_yudisium',
        'ipk',
        'nm_pt_asal',
        'nm_prodi_asal',
        'ro_jns_daftar',
        'ro_jns_keluar',
        'keluar_smt',
        'keterangan',
        'pembiayaan',
        'status',
        'id_wali_dosen',
    ];

    public function waliDosen()
    {
        return $this->belongsTo(DosenData::class, 'id_wali_dosen');
    }

    public function siswa()
    {
        return $this->belongsTo(SiswaData::class, 'id_siswa_data');
    }

    // Alias untuk konsistensi penamaan
    public function siswaData()
    {
        return $this->siswa();
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }
    // public function jenjangPendidikan()
    // {
    //     return $this->belongsTo(JenjangPendidikan::class, 'id_jenjang_pendidikan');
    // }
    public function akademikKrs()
    {
        return $this->hasMany(AkademikKrs::class, 'id_riwayat_pendidikan');
    }

    // public function akademikKrs()
    // {
    //     return $this->hasMany(
    //         AkademikKrs::class,
    //         'id_riwayat_pendidikan',
    //         'id'
    //     );
    // }

    // ref option

    public function statusSiswa()
    {
        return $this->belongsTo(StatusSiswa::class, 'ro_status_siswa');
    }

    public function programSekolah()
    {
        return $this->belongsTo(ProgramSekolah::class, 'ro_program_sekolah');
    }

    public function jenisDaftar()
    {
        return $this->belongsTo(JenisPendaftaran::class, 'ro_jns_daftar');
    }

    public function jenisKeluar()
    {
        return $this->belongsTo(JenisKeluar::class, 'ro_jns_keluar');
    }

    // ── Relasi ke Tugas Akhir ─────────────────────────────────────────────
    public function pengajuanJudul()
    {
        return $this->hasMany(TaPengajuanJudul::class, 'id_riwayat_pendidikan');
    }

    public function seminarProposal()
    {
        return $this->hasMany(TaSeminarProposal::class, 'id_riwayat_pendidikan');
    }

    public function skripsi()
    {
        return $this->hasMany(TaSkripsi::class, 'id_riwayat_pendidikan');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    protected function angkatan(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if ($this->tahunAkademik) {
                    $nama = $this->tahunAkademik->nama;
                    $periode = $this->tahunAkademik->periode;
                    if (str_contains($nama, '/')) {
                        // Hilangkan suffix periode jika ada, misalnya "2024/2025 Genap" -> "2024/2025"
                        $namaClean = explode(' ', $nama)[0];
                        $parts = explode('/', $namaClean);
                        return strtolower($periode) === 'genap' ? $parts[1] : $parts[0];
                    }
                    // Jika tidak ada garis miring, hilangkan saja periodenya
                    return explode(' ', $nama)[0];
                }
                return null;
            }
        );
    }

    /**
     * Centralized Semester Calculation Logic
     * @param mixed $date Reference date (optional, defaults to now)
     * @return int|null
     */
    public function getSemester($date = null)
    {
        if (!$this->tanggal_mulai) return null;

        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai);
        $refDate = $date ? \Carbon\Carbon::parse($date) : now();

        // Jika tanggal referensi sebelum tanggal mulai, tetap semester 1
        if ($refDate->lessThan($startDate)) return 1;

        // Logika Semester Akademik:
        // Ganjil: Juli - Desember (Bulan 7-12) -> +1 semester
        // Genap: Januari - Juni (Bulan 1-6) -> +2 semester
        // Semester dihitung dari selisih tahun akademik

        // $isGenap = $refDate->month <= 6;
        // $academicYear = $isGenap ? $refDate->year - 1 : $refDate->year;
        // $startAcademicYear = $startDate->month <= 6 ? $startDate->year - 1 : $startDate->year;

        // $yearsDiff = $academicYear - $startAcademicYear;
        // $semester = ($yearsDiff * 2) + ($isGenap ? 2 : 1);

        // return max(1, $semester);

        $diffInMonths = $startDate->diffInMonths($refDate);
        return (int) floor($diffInMonths / 6) + 1;
    }

    /**
     * Dynamic Attribute for Current Semester
     */
    protected function currentSemester(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn() => $this->getSemester()
        );
    }
}
