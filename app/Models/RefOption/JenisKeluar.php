<?php

namespace App\Models\RefOption;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKeluar extends Model
{
    use HasFactory;

    /**
     * Tabel yang digunakan
     */
    protected $table = 'reference_option';

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'nama_grup',
        'kode',
        'nilai',
        'status',
        'deskripsi',
    ];

    /**
     * Global scope untuk hanya mengambil grup "Program Kelas"
     */
    protected static function booted()
    {
        static::addGlobalScope('jns_keluar', function ($query) {
            $query->where('nama_grup', 'jns_keluar');
        });
    }

    /**
     * Getter alias "nama" agar sesuai konsep Agama
     */
    public function getNamaAttribute()
    {
        return $this->nilai; // nilai di reference_option dipakai sebagai nama
    }

    /**
     * Getter untuk deskripsi (hanya alias, optional)
     */
    // public function getDeskripsiAttribute()
    // {
    //     return $this->deskripsi;
    // }

    /**
     * Relasi ke tabel Kelas
     * Misal, satu Agama bisa punya banyak Kelas
     */
    public function riwayatPendidikan()
    {
        return $this->hasMany(\App\Models\RiwayatPendidikan::class, 'ro_jns_keluar');
    }

    /**
     * Scope tambahan jika ingin filter aktif saja
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }
}
