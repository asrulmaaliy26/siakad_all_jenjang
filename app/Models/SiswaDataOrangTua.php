<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaDataOrangTua extends Model
{
    use HasFactory;
    protected $table = 'siswa_data_orang_tua';
    protected $fillable = [
        'nama',
        'id_siswa_data',
        // Ayah
        'Nama_Ayah',
        'Tempat_Lhr_Ayah',
        'Tgl_Lhr_Ayah',
        'Bln_Lhr_Ayah',
        'Thn_Lhr_ayah',
        'Agama_Ayah',
        'Gol_Darah_Ayah',
        'Pendidikan_Terakhir_Ayah',
        'Pekerjaan_Ayah',
        'Penghasilan_Ayah',
        'Kebutuhan_Khusus_Ayah',
        'Nomor_KTP_Ayah',
        'Alamat_Ayah',
        'No_Rmh_Ayah',
        'Dusun_Ayah',
        'RT_Ayah',
        'RW_Ayah',
        'Desa_Ayah',
        'Kec_Ayah',
        'Kab_Ayah',
        'Kode_Pos_Ayah',
        'Prov_Ayah',
        'Kewarganegaraan_Ayah',
        // Ibu
        'Nama_Ibu',
        'Tempat_Lhr_Ibu',
        'Tgl_Lhr_Ibu',
        'Bln_Lhr_Ibu',
        'Thn_Lhr_Ibu',
        'Agama_Ibu',
        'Gol_Darah_Ibu',
        'Pendidikan_Terakhir_Ibu',
        'Pekerjaan_Ibu',
        'Penghasilan_Ibu',
        'Kebutuhan_Khusus_Ibu',
        'Nomor_KTP_Ibu',
        'Alamat_Ibu',
        'No_Rmh_Ibu',
        'Dusun_Ibu',
        'RT_Ibu',
        'RW_Ibu',
        'Desa_Ibu',
        'Kec_Ibu',
        'Kab_Ibu',
        'Kode_Pos_Ibu',
        'Prov_Ibu',
        'Kewarganegaraan_Ibu',
        // Kontak
        'No_HP_ayah',
        'No_HP_ibu'
    ];

    public function siswa()
    {
        return $this->belongsTo(SiswaData::class, 'id_siswa_data');
    }

    // Alias untuk konsistensi penamaan
    public function siswaData()
    {
        return $this->siswa();
    }
}
