<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengaturanPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_pendaftaran';

    protected $fillable = [
        'biaya_reguler',
        'biaya_beasiswa',
        'foto_header',
        'foto_banner',
        'deskripsi_pendaftaran',
        'status_pendaftaran',
        'tanggal_buka',
        'tanggal_tutup',
        'id_tahun_akademik',
        'pengumuman',
        'kontak_admin',
        'email_admin',
    ];

    protected $casts = [
        'status_pendaftaran' => 'boolean',
        'tanggal_buka' => 'datetime',
        'tanggal_tutup' => 'datetime',
        'biaya_reguler' => 'decimal:2',
        'biaya_beasiswa' => 'decimal:2',
    ];

    /**
     * Get biaya berdasarkan jalur PMB
     */
    public function getBiayaByJalur($jalurPmb)
    {
        // Ambil nilai dari reference option
        $jalur = \App\Models\ReferenceOption::find($jalurPmb);

        if (!$jalur) {
            return $this->biaya_reguler;
        }

        // Cek apakah jalur adalah beasiswa
        if (stripos($jalur->nilai, 'beasiswa') !== false) {
            return $this->biaya_beasiswa;
        }

        return $this->biaya_reguler;
    }

    /**
     * Check apakah pendaftaran sedang dibuka
     */
    public function isPendaftaranBuka()
    {
        if (!$this->status_pendaftaran) {
            return false;
        }

        $now = now();

        if ($this->tanggal_buka && $now->lt($this->tanggal_buka)) {
            return false;
        }

        if ($this->tanggal_tutup && $now->gt($this->tanggal_tutup)) {
            return false;
        }

        return true;
    }

    /**
     * Relasi ke Tahun Akademik
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    /**
     * Get pengaturan aktif (singleton pattern)
     */
    public static function getAktif()
    {
        return self::firstOrCreate(
            [],
            [
                'biaya_reguler' => 100000,
                'biaya_beasiswa' => 50000,
                'status_pendaftaran' => true,
            ]
        );
    }
}
