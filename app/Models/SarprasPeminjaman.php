<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SarprasPeminjaman extends Model
{
    protected $table = 'sarpras_peminjamen';

    protected $fillable = [
        'sarpras_barang_id',
        'user_id',
        'jumlah_pinjam',
        'tanggal_pinjam',
        'estimasi_kembali',
        'tanggal_kembali',
        'status',
        'keterangan',
        'sarpras_surat_keluar_id',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'estimasi_kembali' => 'datetime',
        'tanggal_kembali' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(SarprasBarang::class, 'sarpras_barang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suratKeluar()
    {
        return $this->belongsTo(SarprasSuratKeluar::class, 'sarpras_surat_keluar_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'Disetujui';
    }

    public function canBePrinted(): bool
    {
        return in_array($this->status, ['Disetujui', 'Dipinjam']) && $this->sarpras_surat_keluar_id !== null;
    }

    public function canBeReturned(): bool
    {
        return $this->status === 'Dipinjam';
    }
}
