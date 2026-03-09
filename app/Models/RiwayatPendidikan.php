<?php

namespace App\Models;

use App\Models\RefOption\StatusSiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SiswaData;
use App\Models\Jurusan;
use App\Models\AkademikKrs;
use App\Models\RefOption\ProgramSekolah;
use App\Models\RefOption\ProgramKelas;
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
        'ro_program_kelas',
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

    public function programKelas()
    {
        return $this->belongsTo(ProgramKelas::class, 'ro_program_kelas');
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
                    if (str_contains($nama, '/')) {
                        // Hilangkan suffix periode jika ada, misalnya "2024/2025 Genap" -> "2024/2025"
                        $namaClean = explode(' ', $nama)[0];
                        $parts = explode('/', $namaClean);
                        return $parts[0]; // Selalu ambil tahun pertama (e.g. 2024/2025 -> 2024)
                    }
                    // Jika tidak ada garis miring, ambil saja bagian pertama
                    return explode(' ', $nama)[0];
                }
                return null;
            }
        );
    }

    /**
     * Centralized Semester Calculation Logic
     *
     * Semester dihitung berdasarkan PERIODE AKADEMIK 6-bulanan:
     *   - Jan–Jun tahun Y  = Periode Genap  (indeks: Y*2 + 0)
     *   - Jul–Des tahun Y  = Periode Ganjil (indeks: Y*2 + 1)
     *
     * Contoh (referensi = Feb 2026 / periode Jan–Jun 2026):
     *   - Masuk Jan–Jun 2026 → Semester 1
     *   - Masuk Jul–Des 2025 → Semester 2
     *   - Masuk Jan–Jun 2025 → Semester 3
     *   - Masuk Jul–Des 2024 → Semester 4
     *
     * @param mixed $date Reference date (optional, defaults to now)
     * @return int|null
     */
    public function getSemester($date = null)
    {
        if (!$this->tanggal_mulai) return null;

        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai);
        $refDate   = $date ? \Carbon\Carbon::parse($date) : now();

        // Hitung indeks periode untuk masing-masing tanggal
        // Jan–Jun = half 0, Jul–Des = half 1
        $startPeriod = $startDate->year * 2 + ($startDate->month <= 6 ? 0 : 1);
        $refPeriod   = $refDate->year   * 2 + ($refDate->month   <= 6 ? 0 : 1);

        $semester = ($refPeriod - $startPeriod) + 1;

        return max(1, $semester);
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
