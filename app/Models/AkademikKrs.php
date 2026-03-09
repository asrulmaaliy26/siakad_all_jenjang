<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AkademikKrs extends Model
{
    use HasFactory;

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

    protected $fillable = [
        'id_riwayat_pendidikan',
        // 'id_kelas',
        'jumlah_sks',
        'tgl_krs',
        'id_tahun_akademik',
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
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
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

            // Logika Periode Akademik:
            // Jan–Jun tahun Y  = Periode Genap  (indeks: Y*2 + 0)
            // Jul–Des tahun Y  = Periode Ganjil (indeks: Y*2 + 1)
            $isGenap      = $now->month <= 6;
            $startPeriod  = $startDate->year * 2 + ($startDate->month <= 6 ? 0 : 1);
            $nowPeriod    = $now->year        * 2 + ($isGenap ? 0 : 1);

            $newSemester = ($nowPeriod - $startPeriod) + 1;

            // Jika untuk alasan tertentu semester hasil hitung <= semester sekarang, paksa naik 1
            if ($newSemester <= $this->semester) {
                $newSemester = $this->semester + 1;
            }

            // 5. Tentukan id Tahun Akademik
            $isGenap      = $now->month <= 6;
            $tahunLabel   = $isGenap
                ? ($now->year - 1) . '/' . $now->year
                : $now->year . '/' . ($now->year + 1);

            $tahunAkademikRecord = \App\Models\TahunAkademik::where('nama', 'like', $tahunLabel . '%')->first();

            // 6. Buat Akademik KRS baru
            $newKrs = self::create([
                'id_riwayat_pendidikan' => $this->id_riwayat_pendidikan,
                'jumlah_sks'            => $newSks,
                'tgl_krs'               => $now,
                'id_tahun_akademik'     => $tahunAkademikRecord?->id,
                'status_bayar'          => 'N',
                'syarat_uts'            => 'N',
                'syarat_uas'            => 'N',
                'syarat_krs'            => 'N',
                'status_aktif'          => 'Y',
            ]);

            // Nonaktifkan record saat ini
            $this->update(['status_aktif' => 'N']);

            return $newKrs;
        });
    }
}
