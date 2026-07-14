<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaData extends Model
{
    use HasFactory;

    protected $table = 'siswa_data';

    protected static function booted()
    {
        static::deleting(function ($siswaData) {
            // Jika sudah ada riwayat pendidikan, cegah penghapusan
            if ($siswaData->riwayatPendidikan()->exists()) {
                throw new \Exception('Data Mahasiswa tidak dapat dihapus karena sudah memiliki Riwayat Pendidikan.');
            }

            // Hapus data pendaftar jika ada
            if ($siswaData->pendaftar) {
                $siswaData->pendaftar->delete();
            }

            // Hapus data orang tua jika ada
            if ($siswaData->orangTua) {
                $siswaData->orangTua->delete();
            }
        });

        static::deleted(function ($siswaData) {
            // Hapus User terkait jika ada, harus dilakukan *setelah* SiswaData terhapus 
            // karena Users adalah parent table (SiswaData memiliki user_id)
            if ($siswaData->user_id) {
                $user = \App\Models\User::find($siswaData->user_id);
                if ($user) {
                    $user->delete();
                }
            }
        });
    }

    protected $fillable = [
        'nama',
        'nama_lengkap',
        'foto_profil',
        'jenis_kelamin',
        'golongan_darah',
        'kota_lahir',
        'tanggal_lahir',
        'alamat',
        'nomor_rumah',
        'dusun',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kabupaten',
        'kode_pos',
        'provinsi',
        'tempat_domisili',
        'jenis_domisili',
        'no_telepon',
        'no_ktp',
        'no_kk',
        'agama', //ro
        'kewarganegaraan',
        'kode_negara',
        'status_pkawin',
        'pekerjaan',
        'biaya_ditanggung',
        'transportasi',
        'status_asal_sekolah',
        'asal_slta',
        'jenis_slta',
        'kejuruan_slta',
        'alamat_lengkap_sekolah_asal',
        'tahun_lulus_slta',
        'nomor_seri_ijazah_slta',
        'nisn',
        'anak_ke',
        'jumlah_saudara',
        'email',
        'penerima_kps',
        'no_kps',
        'kebutuhan_khusus',
        'status_siswa',
        'user_id',
        'npwp',
        'no_hp',
    ];

    public static $pddikti_jenis_tinggal = [
        '1' => 'Bersama orang tua',
        '2' => 'Wali',
        '3' => 'Kost',
        '4' => 'Asrama',
        '5' => 'Panti asuhan',
        '10' => 'Rumah sendiri',
        '99' => 'Lainnya'
    ];

    public static $pddikti_alat_transportasi = [
        '1' => 'Jalan kaki',
        '3' => 'Angkutan umum/bus/pete-pete',
        '4' => 'Mobil/bus antar jemput',
        '5' => 'Kereta api',
        '6' => 'Ojek',
        '7' => 'Andong/bendi/sado/dokar/delman/becak',
        '8' => 'Perahu penyeberangan/rakit/getek',
        '11' => 'Kuda',
        '12' => 'Sepeda',
        '13' => 'Sepeda motor',
        '14' => 'Mobil pribadi',
        '99' => 'Lainnya'
    ];

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'id_siswa_data');
    }

    public function riwayatPendidikanAktif()
    {
        return $this->hasOne(RiwayatPendidikan::class, 'id_siswa_data')
            ->whereIn('status', ['Y', 'Aktif']) // Mendukung 'Y' (lama) dan 'Aktif' (baru)
            ->latest(); // Ambil yang terbaru jika ada lebih dari satu
    }

    // Riwayat pendidikan terbaru apapun statusnya (untuk menampilkan angkatan)
    public function riwayatPendidikanTerbaru()
    {
        return $this->hasOne(RiwayatPendidikan::class, 'id_siswa_data')->latest();
    }

    public function akademikKrs()
    {
        return $this->hasManyThrough(
            AkademikKrs::class,
            RiwayatPendidikan::class,
            'id_siswa_data',           // FK di riwayat_pendidikan
            'id_riwayat_pendidikan',   // FK di akademik_krs
            'id',                      // PK siswa_data
            'id'                       // PK riwayat_pendidikan
        );
    }

    // Relasi 1:1 dengan SiswaDataOrangTua
    public function orangTua()
    {
        return $this->hasOne(SiswaDataOrangTua::class, 'id_siswa_data');
    }

    // Relasi 1:1 dengan SiswaDataPendaftar
    public function pendaftar()
    {
        return $this->hasOne(SiswaDataPendaftar::class, 'id_siswa_data');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
