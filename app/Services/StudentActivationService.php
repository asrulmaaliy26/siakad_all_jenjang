<?php

namespace App\Services;

use App\Models\SiswaData;
use App\Models\RiwayatPendidikan;
use App\Models\AkademikKrs;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StudentActivationService
{
    /**
     * Aktifkan mahasiswa:
     * - Buat RiwayatPendidikan jika belum ada
     * - Buat AkademikKrs perdana
     * - Set status_siswa = 'aktif'
     * - Assign role 'murid', hapus role 'pendaftar'
     */
    public function activateStudent(SiswaData $siswaData): bool
    {
        try {
            $pendaftar = $siswaData->pendaftar;

            if (!$pendaftar) {
                Log::warning("activateStudent: Tidak ada data pendaftar untuk siswa ID {$siswaData->id}");
                return false;
            }

            if (!$pendaftar->id_jurusan || !$pendaftar->ro_program_sekolah) {
                Log::warning("activateStudent: Data jurusan/program belum lengkap untuk siswa ID {$siswaData->id}");
                return false;
            }

            // Cek apakah riwayat pendidikan sudah ada
            $exists = RiwayatPendidikan::where('id_siswa_data', $siswaData->id)
                ->where('id_jurusan', $pendaftar->id_jurusan)
                ->where('ro_program_sekolah', $pendaftar->ro_program_sekolah)
                ->exists();

            if (!$exists) {
                // Cari status siswa "Aktif" di RefOption
                $statusSiswaAktif = \App\Models\RefOption\StatusSiswa::where('nilai', 'Aktif')->first();

                $tahunAkademik = $pendaftar->id_tahun_akademik
                    ? TahunAkademik::find($pendaftar->id_tahun_akademik)
                    : TahunAkademik::where('status', 'Y')->latest()->first();

                $riwayat = RiwayatPendidikan::create([
                    'id_siswa_data'      => $siswaData->id,
                    'id_jurusan'         => $pendaftar->id_jurusan,
                    'ro_program_sekolah' => $pendaftar->ro_program_sekolah,
                    'id_tahun_akademik'  => $tahunAkademik?->id,
                    'tanggal_mulai'      => now(),
                    'mulai_smt'          => 1,
                    'smt_aktif'          => 1,
                    'status'             => 'Aktif',
                    'ro_status_siswa'    => $statusSiswaAktif?->id,
                    'pembiayaan'         => $this->getPembiayaan($pendaftar),
                    'nomor_induk'        => $this->generateNomorInduk($pendaftar),
                ]);

                // Buat Akademik KRS perdana
                AkademikKrs::create([
                    'id_riwayat_pendidikan' => $riwayat->id,
                    'jumlah_sks'            => 24,
                    'tgl_krs'               => now(),
                    'id_tahun_akademik'     => $tahunAkademik?->id,
                    'status_bayar'          => 'N',
                    'syarat_uts'            => 'N',
                    'syarat_uas'            => 'N',
                    'syarat_krs'            => 'N',
                    'status_aktif'          => 'Y',
                ]);

                Log::info("activateStudent: Riwayat Pendidikan & KRS berhasil dibuat untuk siswa ID {$siswaData->id}");
            }

            // Update status siswa
            $siswaData->update(['status_siswa' => 'aktif']);

            // Assign role murid
            $this->assignMuridRole($siswaData);

            return true;
        } catch (\Exception $e) {
            Log::error("activateStudent: Gagal mengaktifkan siswa ID {$siswaData->id}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Nonaktifkan mahasiswa:
     * - Set status di RiwayatPendidikan menjadi 'Tidak Aktif'
     * - Set status_siswa = 'tidak aktif'
     * - Hapus role 'murid', berikan role 'pendaftar' kembali
     */
    public function deactivateStudent(SiswaData $siswaData, string $alasan = 'Tidak Aktif'): bool
    {
        try {
            // Update semua riwayat pendidikan aktif menjadi tidak aktif
            RiwayatPendidikan::where('id_siswa_data', $siswaData->id)
                ->whereIn('status', ['Y', 'Aktif'])
                ->update(['status' => $alasan]);

            // Update status siswa
            $siswaData->update(['status_siswa' => 'tidak aktif']);

            // Cabut role murid
            if ($siswaData->user_id) {
                $user = User::find($siswaData->user_id);
                if ($user) {
                    $user->removeRole('murid');
                    // Kembalikan role pendaftar jika masih ada data pendaftaran
                    if ($siswaData->pendaftar) {
                        $user->assignRole('pendaftar');
                    }
                    Log::info("deactivateStudent: Role murid dicabut untuk user ID {$user->id}");
                }
            }

            Log::info("deactivateStudent: Siswa ID {$siswaData->id} berhasil dinonaktifkan (alasan: {$alasan})");
            return true;
        } catch (\Exception $e) {
            Log::error("deactivateStudent: Gagal menonaktifkan siswa ID {$siswaData->id}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate Nomor Induk (NIM/NIS)
     */
    protected function generateNomorInduk($pendaftar): string
    {
        $tahun = $pendaftar->tahunAkademik
            ? substr($pendaftar->tahunAkademik->nama, 0, 4)
            : date('Y');

        $count = RiwayatPendidikan::where('id_tahun_akademik', $pendaftar->id_tahun_akademik)
            ->count() + 1;

        return $tahun . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Tentukan jenis pembiayaan mahasiswa berdasarkan jalur PMB
     */
    protected function getPembiayaan($pendaftar): string
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

    /**
     * Assign role murid ke user terkait
     */
    protected function assignMuridRole(SiswaData $siswaData): void
    {
        if ($siswaData->user_id) {
            $user = User::find($siswaData->user_id);
            if ($user) {
                $user->assignRole('murid');
                $user->removeRole('pendaftar');
            }
        }
    }
}
