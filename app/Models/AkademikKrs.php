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

    // Attribute to get all Kelas related to this KRS via SiswaDataLJK -> MataPelajaranKelas
    public function getKelasAttribute()
    {
        return $this->siswaDataLjk()
            ->with('mataPelajaranKelas.kelas')
            ->get()
            ->map(fn($ljk) => $ljk->mataPelajaranKelas?->kelas)
            ->filter()
            ->unique('id')
            ->values();
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
     * Lanjutkan Studi: Menonaktifkan KRS saat ini dan membuat KRS baru
     * untuk Tahun Akademik yang dipilih oleh Wali Dosen/Admin.
     *
     * Semester pada KRS baru dihitung berdasarkan urutan KRS aktif mahasiswa,
     * sehingga periode cuti (tidak ada KRS) otomatis tidak terhitung.
     *
     * @param int $targetTahunAkademikId  ID TahunAkademik tujuan yang dipilih
     * @return AkademikKrs  KRS baru yang dibuat
     * @throws \Exception
     */
    public function lanjutkanStudi(int $targetTahunAkademikId): AkademikKrs
    {
        return DB::transaction(function () use ($targetTahunAkademikId) {
            // 1. Validasi: tidak boleh ada KRS aktif lain untuk mahasiswa yang sama di TA tujuan
            $alreadyExists = self::where('id_riwayat_pendidikan', $this->id_riwayat_pendidikan)
                ->where('id_tahun_akademik', $targetTahunAkademikId)
                ->where('id', '!=', $this->id)
                ->exists();

            if ($alreadyExists) {
                throw new \Exception('KRS untuk Tahun Akademik yang dipilih sudah ada untuk mahasiswa ini.');
            }

            // 2. Hitung IPS dari LJK KRS saat ini
            $ljks = $this->siswaDataLjk()
                ->with('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster')
                ->get();

            $totalBobotSks = 0;
            $totalSks = 0;

            foreach ($ljks as $ljk) {
                $sks = (float) ($ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0);
                $bobotNilai = $ljk->bobot;
                $totalBobotSks += ($sks * $bobotNilai);
                $totalSks += $sks;
            }

            $ips = $totalSks > 0 ? ($totalBobotSks / $totalSks) : 0;

            // 3. Tentukan jumlah SKS berdasarkan IPS
            // Aturan: >= 3 → 24 SKS, >= 2 → 18 SKS, < 2 → 12 SKS
            $newSks = 12;
            if ($ips >= 3.0) {
                $newSks = 24;
            } elseif ($ips >= 2.0) {
                $newSks = 18;
            }

            // 4. Buat KRS baru
            $newKrs = self::create([
                'id_riwayat_pendidikan' => $this->id_riwayat_pendidikan,
                'ro_program_kelas'      => $this->ro_program_kelas,
                'jumlah_sks'            => $newSks,
                'tgl_krs'               => now(),
                'id_tahun_akademik'     => $targetTahunAkademikId,
                'status_bayar'          => 'N',
                'syarat_uts'            => 'N',
                'syarat_uas'            => 'N',
                'syarat_krs'            => 'N',
                'status_aktif'          => 'Y',
            ]);

            // 5. Nonaktifkan KRS saat ini
            $this->update(['status_aktif' => 'N']);

            return $newKrs;
        });
    }

    /**
     * @deprecated Gunakan lanjutkanStudi() sebagai gantinya.
     */
    public function deactivateAndCreateNew()
    {
        return $this->lanjutkanStudi(
            \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id
                ?? $this->id_tahun_akademik
        );
    }

    public function getSksDiambilAttribute()
    {
        return $this->siswaDataLjk->sum(function ($ljk) {
            return (float) ($ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0);
        });
    }

    public function getIpsAttribute()
    {
        $ljks = $this->siswaDataLjk()->with('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster')->get();
        $totalBobotSks = 0;
        $totalSks = 0;

        foreach ($ljks as $ljk) {
            $sks = (float) ($ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0);
            $bobotNilai = $ljk->bobot;
            
            $totalBobotSks += ($sks * $bobotNilai);
            $totalSks += $sks;
        }

        return $totalSks > 0 ? round($totalBobotSks / $totalSks, 2) : 0;
    }

    public function getSksTotalAttribute()
    {
        if (!$this->id_riwayat_pendidikan) return 0;

        return \App\Models\SiswaDataLJK::whereHas('akademikKrs', function ($q) {
            $q->where('id_riwayat_pendidikan', $this->id_riwayat_pendidikan)
              ->where('created_at', '<=', $this->created_at);
        })->get()->sum(function ($ljk) {
            return (float) ($ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0);
        });
    }

    public function getIpkAttribute()
    {
        if (!$this->id_riwayat_pendidikan) return 0;

        $ljks = \App\Models\SiswaDataLJK::whereHas('akademikKrs', function ($q) {
            $q->where('id_riwayat_pendidikan', $this->id_riwayat_pendidikan)
              ->where('created_at', '<=', $this->created_at);
        })->with('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster')->get();

        $totalBobotSks = 0;
        $totalSks = 0;

        foreach ($ljks as $ljk) {
            $sks = (float) ($ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0);
            $bobotNilai = $ljk->bobot;
            
            $totalBobotSks += ($sks * $bobotNilai);
            $totalSks += $sks;
        }

        return $totalSks > 0 ? round($totalBobotSks / $totalSks, 2) : 0;
    }
}
