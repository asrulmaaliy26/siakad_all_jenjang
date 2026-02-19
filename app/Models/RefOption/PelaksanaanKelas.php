<?php

namespace App\Models\RefOption;

use App\Models\MataPelajaranKelas;
use App\Models\MataPelajaranKelasDistribusi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferenceOption;

class PelaksanaanKelas extends Model
{
    use HasFactory;

    // Kita tetap pakai tabel refoption
    protected $table = 'reference_option';

    // Kita hanya ingin filter grup 'Ruang Kelas'
    protected static function booted()
    {
        static::addGlobalScope('pelaksanaan_kelas', function ($query) {
            $query->where('nama_grup', 'pelaksanaan_kelas');
        });
    }

    // Field yang bisa diisi
    protected $fillable = ['nama_grup', 'kode', 'nilai', 'status', 'deskripsi'];

    // Alias getter supaya lebih mirip RuangKelas
    public function getNamaAttribute()
    {
        return $this->nilai; // kolom 'nilai' dipakai sebagai 'nama' RuangKelas
    }

    // public function getDeskripsiAttribute()
    // {
    //     return $this->deskripsi;
    // }

    // Contoh relasi (misal ke MataPelajaranKelas)
    public function mataPelajaranKelas()
    {
        return $this->hasMany(MataPelajaranKelas::class, 'ro_pelaksanaan_kelas');
    }
    public function MataPelajaranKelasDistribusi()
    {
        return $this->hasMany(MataPelajaranKelasDistribusi::class, 'ro_pelaksanaan_kelas');
    }
}

// use App\Models\RuangKelas;

// // Ambil semua Ruang Kelas
// $ruangKelas = RuangKelas::all();

// // Bisa dipakai relasi
// foreach ($ruangKelas as $r) {
//     echo $r->nama; // otomatis ambil dari 'nilai'
//     foreach ($r->mataPelajaranKelas as $mp) {
//         echo $mp->nama_mapel;
//     }
// }

// // Bisa buat query
// $ruang = RuangKelas::where('kode', 'R101')->first();
