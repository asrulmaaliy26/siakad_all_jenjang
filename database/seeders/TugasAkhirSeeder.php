<?php

namespace Database\Seeders;

use App\Models\TaPengajuanJudul;
use App\Models\TaSeminarProposal;
use App\Models\TaSkripsi;
use Illuminate\Database\Seeder;

class TugasAkhirSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAkademikId = 1; // 2025/2026

        // Pasangan mahasiswa & pembimbing
        $data = [
            [
                'rp'   => 1, // Nurul Izzah
                'p1'   => 1, // Endah
                'p2'   => 2, // Siti
                'p3'   => null,
                'judul' => 'Implementasi Machine Learning untuk Prediksi Prestasi Mahasiswa',
                'abstrak' => 'Penelitian ini bertujuan mengembangkan model prediksi prestasi akademik mahasiswa menggunakan algoritma machine learning berbasis data historis nilai semester.',
                'status' => 'disetujui',
            ],
            [
                'rp'   => 2, // Hendra
                'p1'   => 3, // Julia
                'p2'   => 4, // Cemeti
                'p3'   => null,
                'judul' => 'Sistem Informasi Manajemen Keuangan Berbasis Web dengan Framework Laravel',
                'abstrak' => 'Perancangan dan implementasi sistem informasi manajemen keuangan yang responsif menggunakan framework Laravel dan Vue.js untuk meningkatkan efisiensi pengelolaan anggaran.',
                'status' => 'selesai',
            ],
            [
                'rp'   => 3, // Febi
                'p1'   => 5, // Fitriani
                'p2'   => 6, // Widya
                'p3'   => null,
                'judul' => 'Analisis Sentimen Media Sosial Menggunakan Deep Learning Berbasis LSTM',
                'abstrak' => 'Penelitian ini menganalisis sentimen komentar media sosial menggunakan model LSTM untuk membantu pengambilan keputusan bisnis berbasis data.',
                'status' => 'revisi',
            ],
            [
                'rp'   => 4, // Mila
                'p1'   => 7, // Adiarja
                'p2'   => 8, // Aslijan
                'p3'   => null,
                'judul' => 'Pengembangan Aplikasi Mobile E-Learning Berbasis Android untuk Siswa SMA',
                'abstrak' => 'Perancangan aplikasi mobile e-learning yang interaktif menggunakan Flutter untuk mendukung pembelajaran jarak jauh siswa SMA.',
                'status' => 'pending',
            ],
            [
                'rp'   => 5, // Agnes
                'p1'   => 9, // Viman
                'p2'   => 10, // Yani
                'p3'   => null,
                'judul' => 'Implementasi IoT untuk Sistem Monitoring Kualitas Udara Real-Time',
                'abstrak' => 'Perancangan sistem IoT berbasis NodeMCU untuk memonitor kualitas udara secara real-time menggunakan sensor MQ-135 dengan notifikasi mobile.',
                'status' => 'disetujui',
            ],
        ];

        // ── TaPengajuanJudul ─────────────────────────────────────────────
        $this->command->info('Seeding TaPengajuanJudul...');
        TaPengajuanJudul::truncate();

        foreach ($data as $item) {
            TaPengajuanJudul::create([
                'id_tahun_akademik'    => $tahunAkademikId,
                'id_riwayat_pendidikan' => $item['rp'],
                'judul'                => $item['judul'],
                'abstrak'              => $item['abstrak'],
                'tgl_pengajuan'        => now()->subMonths(rand(3, 6))->format('Y-m-d'),
                'tgl_ujian'            => in_array($item['status'], ['disetujui', 'selesai'])
                    ? now()->subMonths(rand(1, 2))->format('Y-m-d')
                    : null,
                'ruangan_ujian'        => in_array($item['status'], ['disetujui', 'selesai'])
                    ? 'Ruang Sidang ' . rand(1, 5)
                    : null,
                'tgl_acc_judul'        => in_array($item['status'], ['disetujui', 'selesai'])
                    ? now()->subMonths(rand(2, 4))->format('Y-m-d')
                    : null,
                'file'                 => null,
                'id_dosen_pembimbing_1' => $item['p1'],
                'id_dosen_pembimbing_2' => $item['p2'],
                'id_dosen_pembimbing_3' => $item['p3'],
                'status_dosen_1'       => $item['status'] === 'selesai' ? 'setuju' : 'pending',
                'status_dosen_2'       => $item['status'] === 'selesai' ? 'setuju' : 'pending',
                'status_dosen_3'       => 'pending',
                'nilai_dosen_1'        => $item['status'] === 'selesai' ? rand(75, 90) : null,
                'nilai_dosen_2'        => $item['status'] === 'selesai' ? rand(75, 90) : null,
                'nilai_dosen_3'        => null,
                'ctt_revisi_dosen_1'   => $item['status'] === 'revisi' ? 'Perlu diperbaiki pada bagian metodologi penelitian.' : null,
                'ctt_revisi_dosen_2'   => null,
                'ctt_revisi_dosen_3'   => null,
                'status'               => $item['status'],
            ]);
        }

        $this->command->info('✅ TaPengajuanJudul: ' . count($data) . ' data dibuat.');

        // ── TaSeminarProposal ────────────────────────────────────────────
        $this->command->info('Seeding TaSeminarProposal...');
        TaSeminarProposal::truncate();

        // Hanya mahasiswa yang sudah acc judul yang bisa seminar proposal
        $seminarData = array_slice($data, 0, 4);

        foreach ($seminarData as $i => $item) {
            $statusSeminar  = ['selesai', 'disetujui', 'revisi', 'pending'][$i] ?? 'pending';
            $nilaiD1 = $statusSeminar === 'selesai' ? rand(70, 90) : ($statusSeminar === 'revisi' ? rand(60, 74) : null);
            $nilaiD2 = $statusSeminar === 'selesai' ? rand(70, 90) : ($statusSeminar === 'revisi' ? rand(60, 74) : null);
            $nilaiD3 = $statusSeminar === 'selesai' ? rand(70, 90) : null;

            TaSeminarProposal::create([
                'id_tahun_akademik'    => $tahunAkademikId,
                'id_riwayat_pendidikan' => $item['rp'],
                'judul'                => $item['judul'],
                'abstrak'              => $item['abstrak'],
                'tgl_pengajuan'        => now()->subMonths(rand(1, 3))->format('Y-m-d'),
                'tgl_ujian'            => in_array($statusSeminar, ['selesai', 'disetujui', 'revisi'])
                    ? now()->subWeeks(rand(2, 8))->format('Y-m-d')
                    : null,
                'ruangan_ujian'        => in_array($statusSeminar, ['selesai', 'disetujui', 'revisi'])
                    ? 'Aula Gedung ' . chr(64 + rand(1, 4))
                    : null,
                'tgl_acc_judul'        => now()->subMonths(rand(3, 5))->format('Y-m-d'),
                'file'                 => null,
                'id_dosen_pembimbing_1' => $item['p1'],
                'id_dosen_pembimbing_2' => $item['p2'],
                'id_dosen_pembimbing_3' => $item['p3'],
                'status_dosen_1'       => $statusSeminar === 'selesai' ? 'lulus' : ($statusSeminar === 'revisi' ? 'revisi' : 'pending'),
                'status_dosen_2'       => $statusSeminar === 'selesai' ? 'lulus' : 'pending',
                'status_dosen_3'       => 'pending',
                'nilai_dosen_1'        => $nilaiD1,
                'nilai_dosen_2'        => $nilaiD2,
                'nilai_dosen_3'        => $nilaiD3,
                'ctt_revisi_dosen_1'   => $statusSeminar === 'revisi' ? 'Perbaikan pada bagian tinjauan pustaka dan metodologi.' : null,
                'ctt_revisi_dosen_2'   => null,
                'ctt_revisi_dosen_3'   => null,
                'status'               => $statusSeminar,
            ]);
        }

        $this->command->info('✅ TaSeminarProposal: ' . count($seminarData) . ' data dibuat.');

        // ── TaSkripsi ────────────────────────────────────────────────────
        $this->command->info('Seeding TaSkripsi...');
        TaSkripsi::truncate();

        // Hanya mahasiswa yang sudah lulus seminar proposal
        $skripsiData = array_slice($data, 0, 3);

        foreach ($skripsiData as $i => $item) {
            $statusSkripsi = ['selesai', 'disetujui', 'revisi'][$i] ?? 'pending';
            $nilaiD1 = $statusSkripsi === 'selesai' ? rand(75, 92) : null;
            $nilaiD2 = $statusSkripsi === 'selesai' ? rand(75, 92) : null;
            $nilaiD3 = $statusSkripsi === 'selesai' ? rand(78, 95) : null;
            $nilaiAkhir = $statusSkripsi === 'selesai'
                ? round(($nilaiD1 + $nilaiD2 + $nilaiD3) / 3, 2)
                : null;

            TaSkripsi::create([
                'id_tahun_akademik'    => $tahunAkademikId,
                'id_riwayat_pendidikan' => $item['rp'],
                'judul'                => $item['judul'],
                'abstrak'              => $item['abstrak'],
                'tgl_pengajuan'        => now()->subMonths(rand(1, 2))->format('Y-m-d'),
                'tgl_ujian'            => in_array($statusSkripsi, ['selesai', 'disetujui'])
                    ? now()->subWeeks(rand(1, 4))->format('Y-m-d')
                    : null,
                'ruangan_ujian'        => in_array($statusSkripsi, ['selesai', 'disetujui'])
                    ? 'Aula Utama Gedung ' . chr(64 + rand(1, 3))
                    : null,
                'tgl_acc_skripsi'      => $statusSkripsi === 'selesai'
                    ? now()->subWeeks(rand(1, 2))->format('Y-m-d')
                    : null,
                'file'                 => null,
                'id_dosen_pembimbing_1' => $item['p1'],
                'id_dosen_pembimbing_2' => $item['p2'],
                'id_dosen_pembimbing_3' => $item['p3'],
                'status_dosen_1'       => $statusSkripsi === 'selesai' ? 'lulus' : ($statusSkripsi === 'revisi' ? 'revisi' : 'pending'),
                'status_dosen_2'       => $statusSkripsi === 'selesai' ? 'lulus' : 'pending',
                'status_dosen_3'       => $statusSkripsi === 'selesai' ? 'lulus' : 'pending',
                'nilai_dosen_1'        => $nilaiD1,
                'nilai_dosen_2'        => $nilaiD2,
                'nilai_dosen_3'        => $nilaiD3,
                'ctt_revisi_dosen_1'   => $statusSkripsi === 'revisi' ? 'Perlu pengembangan pada bab kesimpulan dan saran.' : null,
                'ctt_revisi_dosen_2'   => null,
                'ctt_revisi_dosen_3'   => null,
                'nilai_akhir'          => $nilaiAkhir,
                'status'               => $statusSkripsi,
            ]);
        }

        $this->command->info('✅ TaSkripsi: ' . count($skripsiData) . ' data dibuat.');

        $this->command->info('');
        $this->command->info('🎉 Seeding Tugas Akhir selesai!');
    }
}
