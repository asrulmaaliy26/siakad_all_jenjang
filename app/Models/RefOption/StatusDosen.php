<?php

namespace App\Models\RefOption;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusDosen extends Model
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
        static::addGlobalScope('status_dosen', function ($query) {
            $query->where('nama_grup', 'status_dosen');
        });
    }

    /**
     * Getter alias "nama" agar sesuai konsep StatusDosen
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
     * Misal, satu StatusDosen bisa punya banyak Kelas
     */
    public function DosenData()
    {
        return $this->hasMany(\App\Models\DosenData::class, 'ro_status_dosen');
    }

    /**
     * Scope tambahan jika ingin filter aktif saja
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }
}
