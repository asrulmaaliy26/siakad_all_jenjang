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
        // Wali
        'nik_wali',
        'nama_wali',
        'tgl_lahir_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        // Kontak
        'No_HP_ayah',
        'No_HP_ibu'
    ];

    public static $pddikti_pendidikan = [
        '0' => 'Tidak sekolah',
        '1' => 'PAUD',
        '2' => 'TK / sederajat',
        '3' => 'Putus SD',
        '4' => 'SD / sederajat',
        '5' => 'SMP / sederajat',
        '6' => 'SMA / sederajat',
        '7' => 'Paket A',
        '8' => 'Paket B',
        '9' => 'Paket C',
        '20' => 'D1',
        '21' => 'D2',
        '22' => 'D3',
        '23' => 'D4',
        '30' => 'S1',
        '31' => 'Profesi',
        '32' => 'Sp-1',
        '35' => 'S2',
        '36' => 'S2 Terapan',
        '37' => 'Sp-2',
        '40' => 'S3',
        '41' => 'S3 Terapan',
        '90' => 'Non formal',
        '91' => 'Informal',
        '99' => 'Lainnya'
    ];

    public static $pddikti_pekerjaan = [
        '1' => 'Tidak bekerja',
        '2' => 'Nelayan',
        '3' => 'Petani',
        '4' => 'Peternak',
        '5' => 'PNS/TNI/Polri',
        '6' => 'Karyawan Swasta',
        '7' => 'Pedagang Kecil',
        '8' => 'Pedagang Besar',
        '9' => 'Wiraswasta',
        '10' => 'Wirausaha',
        '11' => 'Buruh',
        '12' => 'Pensiunan',
        '13' => 'Peneliti',
        '14' => 'Tim Ahli / Konsultan',
        '15' => 'Magang',
        '16' => 'Tenaga Pengajar /Instruktur / Fasilitator',
        '17' => 'Pimpinan / Manajerial',
        '98' => 'Sudah Meninggal',
        '99' => 'Lainnya'
    ];

    public static $pddikti_penghasilan = [
        '11' => 'Kurang dari Rp. 500,000',
        '12' => 'Rp. 500,000 - Rp. 999,999',
        '13' => 'Rp. 1,000,000 - Rp. 1,999,999',
        '14' => 'Rp. 2,000,000 - Rp. 4,999,999',
        '15' => 'Rp. 5,000,000 - Rp. 20,000,000',
        '16' => 'Lebih dari Rp. 20,000,000'
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
