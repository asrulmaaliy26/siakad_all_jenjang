<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaSeleksiPendaftar extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::deleting(function ($seleksi) {
            $fileFields = ['file_persyaratan', 'file_jawaban'];
            foreach ($fileFields as $field) {
                if (!empty($seleksi->$field)) {
                    $files = is_array($seleksi->$field) ? $seleksi->$field : [$seleksi->$field];
                    foreach ($files as $file) {
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                        }
                    }
                }
            }
        });
    }
    protected $table = 'siswa_seleksi_pendaftar';

    protected $fillable = [
        'id_siswa_data_pendaftar',
        'nama_seleksi',
        'tanggal_seleksi',
        'deskripsi_seleksi',
        'file_persyaratan',
        'file_jawaban',
        'nilai',
        'status_seleksi',
        'keterangan_admin',
    ];

    protected $casts = [
        'tanggal_seleksi' => 'datetime',
        'file_persyaratan' => 'array',
        'file_jawaban' => 'array',
    ];

    public function pendaftar()
    {
        return $this->belongsTo(SiswaDataPendaftar::class, 'id_siswa_data_pendaftar');
    }
}
