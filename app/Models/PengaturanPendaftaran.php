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
        'brosur_pendaftaran',
        'gelombang_1_buka',
        'gelombang_1_tutup',
        'gelombang_1_aktif',
        'gelombang_2_buka',
        'gelombang_2_tutup',
        'gelombang_2_aktif',
        'gelombang_3_buka',
        'gelombang_3_tutup',
        'gelombang_3_aktif',
    ];

    protected $casts = [
        'status_pendaftaran' => 'boolean',
        'tanggal_buka' => 'datetime',
        'tanggal_tutup' => 'datetime',
        'biaya_reguler' => 'decimal:2',
        'biaya_beasiswa' => 'decimal:2',
        'gelombang_1_buka' => 'date',
        'gelombang_1_tutup' => 'date',
        'gelombang_1_aktif' => 'boolean',
        'gelombang_2_buka' => 'date',
        'gelombang_2_tutup' => 'date',
        'gelombang_2_aktif' => 'boolean',
        'gelombang_3_buka' => 'date',
        'gelombang_3_tutup' => 'date',
        'gelombang_3_aktif' => 'boolean',
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
        $today = $now->startOfDay();

        $isAnyWaveOpen = false;

        // Check Gelombang 1
        if ($this->gelombang_1_aktif && $this->gelombang_1_buka && $this->gelombang_1_tutup) {
            if ($today->betweenIncluded($this->gelombang_1_buka, $this->gelombang_1_tutup)) {
                $isAnyWaveOpen = true;
            }
        }

        // Check Gelombang 2
        if ($this->gelombang_2_aktif && $this->gelombang_2_buka && $this->gelombang_2_tutup) {
            if ($today->betweenIncluded($this->gelombang_2_buka, $this->gelombang_2_tutup)) {
                $isAnyWaveOpen = true;
            }
        }

        // Check Gelombang 3
        if ($this->gelombang_3_aktif && $this->gelombang_3_buka && $this->gelombang_3_tutup) {
            if ($today->betweenIncluded($this->gelombang_3_buka, $this->gelombang_3_tutup)) {
                $isAnyWaveOpen = true;
            }
        }

        // Jika salah satu gelombang aktif dan sesuai tanggal, maka pendaftaran buka
        if ($isAnyWaveOpen) {
            return true;
        }

        // Fallback backward compatibility: Jika tidak ada gelombang yang diset, 
        // cek tanggal_buka dan tanggal_tutup utama jika salah satu exist
        if (!$this->gelombang_1_buka && !$this->gelombang_2_buka && !$this->gelombang_3_buka) {
            if ($this->tanggal_buka && $now->lt($this->tanggal_buka)) {
                return false;
            }
            if ($this->tanggal_tutup && $now->gt($this->tanggal_tutup)) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Get Gelombang Aktif saat ini
     */
    public function getGelombangAktif()
    {
        $today = now()->startOfDay();

        if ($this->gelombang_1_aktif && $this->gelombang_1_buka && $this->gelombang_1_tutup && $today->betweenIncluded($this->gelombang_1_buka, $this->gelombang_1_tutup)) {
            return 'Gelombang 1';
        }
        if ($this->gelombang_2_aktif && $this->gelombang_2_buka && $this->gelombang_2_tutup && $today->betweenIncluded($this->gelombang_2_buka, $this->gelombang_2_tutup)) {
            return 'Gelombang 2';
        }
        if ($this->gelombang_3_aktif && $this->gelombang_3_buka && $this->gelombang_3_tutup && $today->betweenIncluded($this->gelombang_3_buka, $this->gelombang_3_tutup)) {
            return 'Gelombang 3';
        }
        return 'Pendaftaran Reguler';
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
