<?php

namespace App\Models;

use App\Models\RefOption\StatusSiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SiswaData;
use App\Models\Jurusan;
use App\Models\JenjangPendidikan;
use App\Models\AkademikKrs;
use App\Models\RefOption\ProgramSekolah;
use App\Models\RefOption\JenisPendaftaran;
use App\Models\RefOption\JenisKeluar;

class RiwayatPendidikan extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: riwayat_pendidikan -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }

    protected $table = 'riwayat_pendidikan';
    protected $fillable = [
        'id_siswa_data',
        // 'id_jenjang_pendidikan', // Derived from Jurusan
        'id_jurusan',
        'ro_program_sekolah',
        'nomor_induk',
        'ro_status_siswa',
        'angkatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'foto_profil',
        'mulai_smt',
        'smt_aktif',
        'th_masuk',
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

        if ($refDate->lessThan($startDate)) return 1;

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
