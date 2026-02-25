<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AkademikKrs extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'akademik_krs';

    protected static function booted()
    {
        static::deleting(function ($krs) {
            // Delete related LJK records
            $krs->siswaDataLjk()->delete();
            // Delete related attendance records
            $krs->absensiSiswa()->delete();
        });
    }

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: akademik_krs -> riwayat_pendidikan -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('riwayatPendidikan.jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }
    protected $fillable = [
        'id_riwayat_pendidikan',
        // 'id_kelas',
        'jumlah_sks',
        'tgl_krs',
        'kode_tahun',
        'status_bayar',
        'syarat_uts',
        'syarat_uas',
        'syarat_krs',
        'syarat_lain',
        'kwitansi_krs', // berkas / file uploud
        'berkas_lain', // berkas / file uploud
        'status_aktif',
    ];

    protected $casts = [
        'kwitansi_krs' => 'array',
        'berkas_lain' => 'array',
    ];

    public function riwayatPendidikan()
    {
        return $this->belongsTo(RiwayatPendidikan::class, 'id_riwayat_pendidikan');
    }

    // Relationship to Kelas via SiswaDataLJK
    public function kelas()
    {
        return $this->hasManyThrough(
            Kelas::class,
            SiswaDataLJK::class,
            'id_akademik_krs',
            'id',
            'id',
            'id_mata_pelajaran_kelas'
        );
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'kode_tahun', 'nama');
    }

    public function siswaDataLjk()
    {
        return $this->hasMany(SiswaDataLJK::class, 'id_akademik_krs');
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'id_krs');
    }

    /**
     * Deactivates current KRS and creates a new one for the next semester.
     * 
     * @return AkademikKrs
     * @throws \Exception
     */
    public function deactivateAndCreateNew()
    {
        return DB::transaction(function () {
            // 1. Validasi: Apakah ada KRS lain di riwayat pendidikan yang sama yang masih disetujui (syarat_krs = Y)
            $hasOtherApproved = self::where('id_riwayat_pendidikan', $this->id_riwayat_pendidikan)
                ->where('id', '!=', $this->id)
                ->where('syarat_krs', 'Y')
                ->exists();

            if ($hasOtherApproved) {
                throw new \Exception('Gagal menonaktifkan: Terdapat data KRS lain untuk mahasiswa ini yang masih berstatus Disetujui.');
            }

            // 2. Hitung Nilai Akhir / IPS dari LJK data
            // Rata-rata Nilai_Akhir dari semua LJK di KRS ini
            $ips = $this->siswaDataLjk()->avg('Nilai_Akhir') ?? 0;

            // 3. Tentukan jumlah SKS berdasarkan IPS
            // Aturan: 3 -> 24 SKS, 2 -> 18 SKS, <2 -> 12 SKS
            $newSks = 12;
            if ($ips >= 3.0) {
                $newSks = 24;
            } elseif ($ips >= 2.0) {
                $newSks = 18;
            }

            // 4. Hitung Semester berdasarkan tanggal_mulai riwayat pendidikan
            $riwayat = $this->riwayatPendidikan;
            if (!$riwayat || !$riwayat->tanggal_mulai) {
                throw new \Exception('Data riwayat pendidikan atau tanggal mulai tidak ditemukan.');
            }

            $startDate = Carbon::parse($riwayat->tanggal_mulai);
            $now = now();

            // Perbaikan Logika Tahun Akademik: 
            // Jika Jan-Jun, maka tahun akademik masih tahun sebelumnya (Genap)
            // Jika Jul-Des, maka tahun akademik adalah tahun sekarang (Ganjil)
            $isGenap = $now->month <= 6;
            $academicYear = $isGenap ? $now->year - 1 : $now->year;
            $yearsDiff = $academicYear - $startDate->year;

            $newSemester = ($yearsDiff * 2) + ($isGenap ? 2 : 1);

            // Jika untuk alasan tertentu semester hasil hitung <= semester sekarang, paksa naik 1
            if ($newSemester <= $this->semester) {
                $newSemester = $this->semester + 1;
            }

            // 5. Tentukan Kode Tahun (Tahun Akademik)
            // Format: 2024/2025
            $tahunAkademik = $isGenap
                ? ($now->year - 1) . '/' . $now->year
                : $now->year . '/' . ($now->year + 1);

            // 6. Buat Akademik KRS baru
            $newKrs = self::create([
                'id_riwayat_pendidikan' => $this->id_riwayat_pendidikan,
                // 'id_kelas' => $this->id_kelas, // Removed
                'jumlah_sks' => $newSks,
                'tgl_krs' => $now,
                'kode_tahun' => $tahunAkademik,
                'status_bayar' => 'N',
                'syarat_uts' => 'N',
                'syarat_uas' => 'N',
                'syarat_krs' => 'N',
                'status_aktif' => 'Y',
            ]);

            // Nonaktifkan record saat ini
            $this->update(['status_aktif' => 'N']);

            return $newKrs;
        });
    }
}
