<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SarprasSuratKeluar extends Model
{
    protected $table = 'sarpras_surat_keluars';

    protected $fillable = [
        'sarpras_surat_kategori_id',
        'user_id',
        'nomor_surat',
        'perihal',
        'tujuan',
        'tanggal_surat',
        'isi_surat',
        'file_path',
        'status',
        'tahun_akademik_id',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->nomor_surat)) {
                $model->nomor_surat = static::generateNomorSurat($model->sarpras_surat_kategori_id, $model->tanggal_surat);
            }
        });
    }

    public static function generateNomorSurat(int $kategoriId, $tanggal = null): string
    {
        $kategori = SarprasSuratKategori::find($kategoriId);
        if (!$kategori) return 'TEMP-' . uniqid();

        $date = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();
        $year = $date->year;
        $month = $date->format('m');
        
        $counter = self::where('sarpras_surat_kategori_id', $kategoriId)
            ->whereYear('tanggal_surat', $year)
            ->count() + 1;
            
        $format = $kategori->format_nomor ?: '{counter}/UN/{kode}/{year}';
        
        return str_replace(
            ['{counter}', '{kode}', '{year}', '{month}'],
            [sprintf('%03d', $counter), $kategori->kode, $year, $month],
            $format
        );
    }

    public function kategori()
    {
        return $this->belongsTo(SarprasSuratKategori::class, 'sarpras_surat_kategori_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
