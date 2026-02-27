<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'id_riwayat_pendidikan',
        'id_tahun_akademik',
        'jenis_surat',
        'keperluan',
        'status',
        'catatan_admin',
        'file_pendukung',
        'file_hasil',
    ];

    public function riwayatPendidikan()
    {
        return $this->belongsTo(RiwayatPendidikan::class, 'id_riwayat_pendidikan');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    public static function getJenisOptions(): array
    {
        return [
            'cuti' => 'Surat Cuti',
            'rekomendasi_seminar' => 'Surat Rekomendasi Seminar Proposal',
            'ket_aktif' => 'Surat Keterangan Mahasiswa Aktif',
            'pindah' => 'Surat Keterangan Pindah Kuliah',
            'izin_penelitian' => 'Surat Izin Penelitian',
            'izin_pkl' => 'Surat Izin Prakerin/PKL',
            'dispensasi' => 'Surat Dispensasi',
            'ket_lulus_sementara' => 'Surat Keterangan Lulus Sementara',
            'lainnya' => 'Lainnya',
        ];
    }
}
