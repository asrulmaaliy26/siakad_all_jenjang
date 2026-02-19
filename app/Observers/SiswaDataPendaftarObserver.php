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
        // Cek apakah Status_Kelulusan berubah menjadi 'Y' (Lulus)
        if ($pendaftar->isDirty('Status_Kelulusan') && $pendaftar->Status_Kelulusan === 'Y') {
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
            $existingRiwayat = RiwayatPendidikan::where('id_siswa_data', $pendaftar->id_siswa_data)
                ->where('id_jenjang_pendidikan', $pendaftar->id_jenjang_pendidikan)
                ->first();

            if ($existingRiwayat) {
                Log::info("Riwayat pendidikan sudah ada untuk pendaftar ID: {$pendaftar->id}");
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
                'id_jenjang_pendidikan' => $pendaftar->id_jenjang_pendidikan,
                'id_jurusan' => $jurusan?->id,
                'nomor_induk' => $nomorInduk,
                'ro_status_siswa' => 1, // Status Aktif
                'angkatan' => $pendaftar->Tahun_Masuk ?? date('Y'),
                'tanggal_mulai' => now(),
                'th_masuk' => $pendaftar->Tahun_Masuk ?? date('Y'),
                'mulai_smt' => 1, // Semester 1
                'smt_aktif' => 1,
                'pembiayaan' => $this->getPembiayaan($pendaftar),
                'status' => 'Aktif',
            ]);

            Log::info("Riwayat pendidikan berhasil dibuat untuk pendaftar ID: {$pendaftar->id}, Riwayat ID: {$riwayatPendidikan->id}");

            // Update status pendaftar
            $pendaftar->update([
                'Status_Pendaftaran' => 'Y',
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal membuat riwayat pendidikan untuk pendaftar ID: {$pendaftar->id}. Error: " . $e->getMessage());
        }
    }

    /**
     * Generate nomor induk (NIM/NIS)
     */
    protected function generateNomorInduk(SiswaDataPendaftar $pendaftar): string
    {
        $tahun = $pendaftar->Tahun_Masuk ?? date('Y');
        $jenjang = $pendaftar->jenjangPendidikan?->nama ?? 'XX';

        // Hitung jumlah mahasiswa di tahun dan jenjang yang sama
        $count = RiwayatPendidikan::where('th_masuk', $tahun)
            ->where('id_jenjang_pendidikan', $pendaftar->id_jenjang_pendidikan)
            ->count() + 1;

        // Format: TAHUN + KODE_JENJANG + URUTAN
        // Contoh: 2024S10001, 2024MA0001
        $kodeJenjang = strtoupper(substr($jenjang, 0, 2));
        $urutan = str_pad($count, 4, '0', STR_PAD_LEFT);

        return $tahun . $kodeJenjang . $urutan;
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
}
