<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaDataPendaftar extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'siswa_data_pendaftar';

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: siswa_data_pendaftar -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }

    protected $attributes = [
        'Status_Kelulusan' => 'B', // Default Proses
        'Status_Pendaftaran' => 'B', // Default Proses
    ];

    protected $fillable = [
        'nama',
        'id_siswa_data',
        // Data Dasar Pendaftaran
        'Nama_Lengkap',
        'No_Pendaftaran',
        'Tahun_Masuk',
        'Tgl_Daftar',
        'ro_program_sekolah', // ID dari reference_option (nama_grup: program_sekolah)
        // 'id_jenjang_pendidikan', // FK ke jenjang_pendidikan, dikelola via Jurusan
        'Kelas_Program_Kuliah',
        'id_jurusan',
        'Prodi_Pilihan_1',
        'Prodi_Pilihan_2',
        'Jalur_PMB', // ID dari reference_option (nama_grup: jalur_pmb)
        'Bukti_Jalur_PMB',
        'Jenis_Pembiayaan',
        'Bukti_Jenis_Pembiayaan',
        'Status_Pendaftaran',

        // Data Mutasi/Transfer
        'NIMKO_Asal',
        'Prodi_Asal',
        'PT_Asal',
        'Jml_SKS_Asal',
        'IPK_Asal',
        'Semester_Asal',
        'Pengantar_Mutasi',
        'Transkip_Asal',

        // Dokumen
        'Legalisir_Ijazah',
        'Legalisir_SKHU',
        'Copy_KTP',

        // Foto
        'Foto_BW_3x3',
        'Foto_BW_3x4',
        'Foto_Warna_5x6',
        'File_Foto_Berwarna',
        'Nama_File_Foto',

        // Tes Tulis
        'Tgl_Tes_Tulis',
        'N_Agama',
        'N_Umum',
        'N_Psiko',
        'N_Jumlah_Tes_Tulis',
        'N_Rerata_Tes_Tulis',

        // Tes Lisan
        'Tgl_Tes_Lisan',
        'N_Potensi_Akademik',
        'N_Baca_al_Quran',
        'N_Baca_Kitab_Kuning',
        'N_Jumlah_Tes_Lisan',
        'N_Rearata_Tes_Lisan',

        // Kelulusan
        'Jumlah_Nilai',
        'Rata_Rata',
        'Status_Kelulusan',
        'Rekomendasi_1',
        'Rekomendasi_2',
        'No_SK_Kelulusan',
        'Tgl_SK_Kelulusan',
        'Diterima_di_Prodi',

        // Pembayaran & Verifikasi
        'Biaya_Pendaftaran',
        'Bukti_Biaya_Daftar',
        'status_valid',
        'verifikator',
        'reff'
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

    // Relasi ke Reference Option untuk Program Sekolah
    public function programSekolahRef()
    {
        return $this->belongsTo(ReferenceOption::class, 'ro_program_sekolah', 'id');
    }

    // Relasi ke Reference Option untuk Jalur PMB
    public function jalurPmbRef()
    {
        return $this->belongsTo(ReferenceOption::class, 'Jalur_PMB', 'id');
    }

    // Relasi ke Jenjang Pendidikan
    // public function jenjangPendidikan()
    // {
    //     return $this->belongsTo(JenjangPendidikan::class, 'id_jenjang_pendidikan');
    // }

    // Relasi ke Jurusan
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }
}
