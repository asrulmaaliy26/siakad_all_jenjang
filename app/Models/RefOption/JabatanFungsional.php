<?php

namespace App\Models\RefOption;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanFungsional extends Model
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
        static::addGlobalScope('jabatan_fungsional', function ($query) {
            $query->where('nama_grup', 'jabatan_fungsional');
        });
    }

    /**
     * Getter alias "nama" agar sesuai konsep JabatanFungsional
     */
    public function getNamaAttribute()
    {
        return $this->nilai; // nilai di reference_option dipakai sebagai nama
    }

    /**
     * Getter untuk deskripsi (hanya alias, optional)
     */
    public function getDeskripsiAttribute()
    {
        return $this->deskripsi;
    }

    /**
     * Relasi ke tabel Kelas
     * Misal, satu JabatanFungsional bisa punya banyak Kelas
     */
    public function DosenData()
    {
        return $this->hasMany(\App\Models\DosenData::class, 'ro_jabatan');
    }

    /**
     * Scope tambahan jika ingin filter aktif saja
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }
}
