<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAkademik extends Model
{
    use HasFactory;
    protected $table = 'tahun_akademik';
    protected $fillable = ['nama', 'periode', 'status', 'kode_pddikti'];

    protected static function booted()
    {
        static::saving(function ($ta) {
            $tahunString = $ta->getRawOriginal('nama') ?? $ta->nama; // Hindari getter modification jika bisa, atau pakai getRawOriginal
            $tahunString = str_replace(' ' . $ta->periode, '', $tahunString); // Bersihkan ' Genap' dll jika ada
            
            $tahunAwal = substr(trim($tahunString), 0, 4); // "2024" dari "2024/2025"

            $digit = '1';
            if (strtolower($ta->periode) === 'ganjil') {
                $digit = '1';
            } elseif (strtolower($ta->periode) === 'genap') {
                $digit = '2';
            } elseif (strtolower($ta->periode) === 'pendek') {
                $digit = '3';
            }

            if (is_numeric($tahunAwal)) {
                $ta->kode_pddikti = $tahunAwal . $digit;
            }
        });
    }

    public function getNamaAttribute($value)
    {
        if (!$value) return $value;
        // Tampilkan format: 2024/2025 Genap
        if (str_contains($value, ' ' . $this->periode)) {
            return $value;
        }
        return $value . ' ' . $this->periode;
    }

    public function pengaturanPendaftaran()
    {
        return $this->hasMany(PengaturanPendaftaran::class, 'id_tahun_akademik');
    }

    public function pengajuanJudul()
    {
        return $this->hasMany(TaPengajuanJudul::class, 'id_tahun_akademik');
    }

    public function seminarProposal()
    {
        return $this->hasMany(TaSeminarProposal::class, 'id_tahun_akademik');
    }

    public function skripsi()
    {
        return $this->hasMany(TaSkripsi::class, 'id_tahun_akademik');
    }
}
