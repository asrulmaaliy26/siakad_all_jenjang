<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaDataPendaftar extends Model
{
    use HasFactory;

    protected static $isDeletingRelated = false;

    protected static function booted()
    {
        static::deleting(function ($pendaftar) {
            if (static::$isDeletingRelated) {
                return;
            }

            try {
                static::$isDeletingRelated = true;
                if ($pendaftar->siswa && !$pendaftar->siswa->riwayatPendidikan()->exists()) {
                    $pendaftar->siswa->delete();
                }
            } finally {
                static::$isDeletingRelated = false;
            }
        });
    }

    protected $table = 'siswa_data_pendaftar';


    protected $attributes = [
        'Status_Kelulusan_Seleksi' => 'B', // Default Proses
        'Status_Pendaftaran' => 'B', // Default Proses
    ];

    protected $fillable = [
        'nama',
        'id_siswa_data',
        // Data Dasar Pendaftaran
        'Nama_Lengkap',
        'No_Pendaftaran',
        'id_tahun_akademik',
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
        'Status_Kelulusan_Seleksi',
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
        'id_referal_code'
    ];

    protected $casts = [
        'Legalisir_Ijazah'   => 'array',
        'Legalisir_SKHU'     => 'array',
        'Copy_KTP'           => 'array',
        'Foto_BW_3x3'        => 'array',
        'Foto_BW_3x4'        => 'array',
        'Foto_Warna_5x6'     => 'array',
        'File_Foto_Berwarna' => 'array',
    ];

    public function referalCode()
    {
        return $this->belongsTo(ReferalCode::class, 'id_referal_code');
    }

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

    // Relasi ke Jurusan
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    public function seleksi()
    {
        return $this->hasMany(SiswaSeleksiPendaftar::class, 'id_siswa_data_pendaftar');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }
}
