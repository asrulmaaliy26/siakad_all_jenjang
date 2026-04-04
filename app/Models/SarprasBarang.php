<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SarprasBarang extends Model
{
    protected $fillable = [
        'kode_barang', 'nama_barang', 'merek', 'jumlah', 'sarpras_kategori_id', 
        'id_jurusan', 'kondisi', 'status_penggunaan', 'tanggal_pengadaan', 
        'keterangan', 'lampiran'
    ];

    protected $casts = [
        'lampiran' => 'array',
        'tanggal_pengadaan' => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(SarprasKategori::class, 'sarpras_kategori_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(\App\Models\Jurusan::class, 'id_jurusan');
    }

    public function peminjamans()
    {
        return $this->hasMany(SarprasPeminjaman::class, 'sarpras_barang_id');
    }
}
