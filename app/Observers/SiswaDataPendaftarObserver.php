<?php

namespace App\Observers;

use App\Models\SiswaDataPendaftar;
use App\Models\RiwayatPendidikan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SiswaDataPendaftarObserver
{
    /**
     * Handle the SiswaDataPendaftar "updated" event.
     */
    public function updated(SiswaDataPendaftar $pendaftar): void
    {
        // Cek apakah Status_Pendaftaran berubah menjadi 'Y' (Diterima / Aktif)
        if ($pendaftar->isDirty('Status_Pendaftaran') && $pendaftar->Status_Pendaftaran === 'Y') {
            $this->buatRiwayatPendidikan($pendaftar);
        }
    }

    /**
     * Buat Riwayat Pendidikan untuk pendaftar yang lulus
     */
    protected function buatRiwayatPendidikan(SiswaDataPendaftar $pendaftar): void
    {
        try {
            // Cek apakah sudah ada riwayat pendidikan
            $existingRiwayat = RiwayatPendidikan::where('id_siswa_data', $pendaftar->id_siswa_data)->first();

            if ($existingRiwayat) {
                Log::info("Riwayat pendidikan sudah ada untuk pendaftar ID: {$pendaftar->id}");
                $this->assignMuridRole($pendaftar);
                return;
            }

            // Ambil data siswa
            $siswaData = $pendaftar->siswa;
            if (!$siswaData) {
                Log::error("SiswaData tidak ditemukan untuk pendaftar ID: {$pendaftar->id}");
                return;
            }

            // Cari jurusan berdasarkan Diterima_di_Prodi atau Prodi_Pilihan_1
            $namaProdi = $pendaftar->Diterima_di_Prodi ?? $pendaftar->Prodi_Pilihan_1;
            $jurusan = null;

            if ($namaProdi) {
                $jurusan = \App\Models\Jurusan::where('nama_jurusan', 'like', "%{$namaProdi}%")->first();
            }

            // Generate nomor induk (NIM/NIS)
            $nomorInduk = $this->generateNomorInduk($pendaftar);

            // Buat Riwayat Pendidikan
            $riwayatPendidikan = RiwayatPendidikan::create([
                'id_siswa_data' => $pendaftar->id_siswa_data,
                'id_jurusan' => $jurusan?->id,
                'nomor_induk' => $nomorInduk,
                'ro_status_siswa' => 1, // Status Aktif
                'id_tahun_akademik' => $pendaftar->id_tahun_akademik,
                'tanggal_mulai' => now(),
                'mulai_smt' => 1, // Semester 1
                'smt_aktif' => 1,
                'pembiayaan' => $this->getPembiayaan($pendaftar),
                'status' => 'Aktif',
            ]);

            Log::info("Riwayat pendidikan berhasil dibuat untuk pendaftar ID: {$pendaftar->id}, Riwayat ID: {$riwayatPendidikan->id}");

            // Assign role
            $this->assignMuridRole($pendaftar);
        } catch (\Exception $e) {
            Log::error("Gagal membuat riwayat pendidikan untuk pendaftar ID: {$pendaftar->id}. Error: " . $e->getMessage());
        }
    }

    /**
     * Generate nomor induk (NIM/NIS)
     */
    protected function generateNomorInduk(SiswaDataPendaftar $pendaftar): string
    {
        $tahun = $pendaftar->tahunAkademik ? substr($pendaftar->tahunAkademik->nama, 0, 4) : date('Y');

        // Hitung jumlah mahasiswa di tahun yang sama
        $count = RiwayatPendidikan::where('id_tahun_akademik', $pendaftar->id_tahun_akademik)
            ->count() + 1;

        // Format: TAHUN + URUTAN
        // Contoh: 20240001
        $urutan = str_pad($count, 6, '0', STR_PAD_LEFT);

        return $tahun . $urutan;
    }

    /**
     * Get pembiayaan berdasarkan jalur PMB
     */
    protected function getPembiayaan(SiswaDataPendaftar $pendaftar): ?string
    {
        if (!$pendaftar->jalurPmbRef) {
            return 'Mandiri';
        }

        $jalur = $pendaftar->jalurPmbRef->nilai;

        if (stripos($jalur, 'beasiswa') !== false) {
            return 'Beasiswa';
        }

        return 'Mandiri';
    }

    protected function assignMuridRole(SiswaDataPendaftar $pendaftar): void
    {
        try {
            $siswaData = $pendaftar->siswa;
            if ($siswaData && $siswaData->user_id) {
                $user = User::find($siswaData->user_id);
                if ($user) {
                    $user->assignRole('murid');
                    $user->removeRole('pendaftar');
                    Log::info("Role murid (Aktif) berhasil ditambahkan dan role pendaftar dihapus untuk user ID: {$user->id}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengubah role untuk pendaftar ID: {$pendaftar->id}. Error: " . $e->getMessage());
        }
    }
}
