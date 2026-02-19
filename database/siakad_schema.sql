-- ============================================
-- SIAKAD Database Schema
-- Sesuai dengan Models Laravel
-- ============================================

-- Drop tables if exists (dalam urutan yang benar)
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `siswa_data_ljk`;
DROP TABLE IF EXISTS `absensi_siswa`;
DROP TABLE IF EXISTS `mata_pelajaran_kelas`;
DROP TABLE IF EXISTS `akademik_krs`;
DROP TABLE IF EXISTS `riwayat_pendidikan`;
DROP TABLE IF EXISTS `siswa_data`;
DROP TABLE IF EXISTS `siswa_data_pendaftar`;
DROP TABLE IF EXISTS `siswa_data_orang_tua`;
DROP TABLE IF EXISTS `kelas`;
DROP TABLE IF EXISTS `mata_pelajaran_kurikulum`;
DROP TABLE IF EXISTS `kurikulum`;
DROP TABLE IF EXISTS `mata_pelajaran_master`;
DROP TABLE IF EXISTS `dosen_data`;
DROP TABLE IF EXISTS `jurusan`;
DROP TABLE IF EXISTS `fakultas`;
DROP TABLE IF EXISTS `tahun_akademik`;
DROP TABLE IF EXISTS `jenjang_pendidikan`;
DROP TABLE IF EXISTS `reference_option`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS=1;

-- ============================================
-- 1. REFERENCE OPTION (HARUS PERTAMA!)
-- ============================================
CREATE TABLE `reference_option` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_grup` varchar(100) NOT NULL COMMENT 'Nama grup option (program_kelas, ruang_kelas, status_siswa, dll)',
  `kode` varchar(50) DEFAULT NULL COMMENT 'Kode singkat',
  `nilai` varchar(255) NOT NULL COMMENT 'Nilai/Label yang ditampilkan',
  `status` enum('Y','N') DEFAULT 'Y' COMMENT 'Status aktif',
  `deskripsi` text DEFAULT NULL COMMENT 'Deskripsi tambahan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_nama_grup` (`nama_grup`),
  KEY `idx_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. JENJANG PENDIDIKAN
-- ============================================
CREATE TABLE `jenjang_pendidikan` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL COMMENT 'S1, S2, S3, D3, dll',
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TAHUN AKADEMIK
-- ============================================
CREATE TABLE `tahun_akademik` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(20) NOT NULL COMMENT 'Contoh: 2024/2025',
  `periode` enum('Ganjil','Genap') NOT NULL,
  `status` enum('Y','N') DEFAULT 'N' COMMENT 'Y = Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. FAKULTAS
-- ============================================
CREATE TABLE `fakultas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. JURUSAN / PROGRAM STUDI
-- ============================================
CREATE TABLE `jurusan` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `id_fakultas` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_jurusan_fakultas` (`id_fakultas`),
  CONSTRAINT `fk_jurusan_fakultas` FOREIGN KEY (`id_fakultas`) REFERENCES `fakultas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. DOSEN DATA
-- ============================================
CREATE TABLE `dosen_data` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `foto_profil` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `NIPDN` varchar(50) DEFAULT NULL,
  `NIY` varchar(50) DEFAULT NULL,
  `gelar_depan` varchar(50) DEFAULT NULL,
  `gelar_belakang` varchar(50) DEFAULT NULL,
  `ro_pangkat_gol` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (pangkat)',
  `ro_jabatan` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (jabatan_fungsional)',
  `id_jurusan` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `ibu_kandung` varchar(255) DEFAULT NULL,
  `kewarganegaraan` varchar(100) DEFAULT NULL,
  `Alamat` text DEFAULT NULL,
  `status_kawin` enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') DEFAULT NULL,
  `ro_status_dosen` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (status_dosen)',
  `ro_agama` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (agama)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dosen_jurusan` (`id_jurusan`),
  KEY `fk_dosen_pangkat` (`ro_pangkat_gol`),
  KEY `fk_dosen_jabatan` (`ro_jabatan`),
  KEY `fk_dosen_status` (`ro_status_dosen`),
  KEY `fk_dosen_agama` (`ro_agama`),
  CONSTRAINT `fk_dosen_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_pangkat` FOREIGN KEY (`ro_pangkat_gol`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_jabatan` FOREIGN KEY (`ro_jabatan`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_status` FOREIGN KEY (`ro_status_dosen`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_agama` FOREIGN KEY (`ro_agama`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. MATA PELAJARAN MASTER
-- ============================================
CREATE TABLE `mata_pelajaran_master` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `id_jurusan` bigint(20) UNSIGNED NOT NULL,
  `bobot` int(11) DEFAULT 3 COMMENT 'SKS',
  `jenis` enum('wajib','peminatan') DEFAULT 'wajib',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_matkul_master_jurusan` (`id_jurusan`),
  CONSTRAINT `fk_matkul_master_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. KURIKULUM
-- ============================================
CREATE TABLE `kurikulum` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `id_jurusan` bigint(20) UNSIGNED NOT NULL,
  `id_tahun_akademik` bigint(20) UNSIGNED NOT NULL,
  `id_jenjang_pendidikan` bigint(20) UNSIGNED NOT NULL,
  `status_aktif` enum('Y','N') DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kurikulum_jurusan` (`id_jurusan`),
  KEY `fk_kurikulum_tahun` (`id_tahun_akademik`),
  KEY `fk_kurikulum_jenjang` (`id_jenjang_pendidikan`),
  CONSTRAINT `fk_kurikulum_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kurikulum_tahun` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kurikulum_jenjang` FOREIGN KEY (`id_jenjang_pendidikan`) REFERENCES `jenjang_pendidikan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. MATA PELAJARAN KURIKULUM
-- ============================================
CREATE TABLE `mata_pelajaran_kurikulum` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_kurikulum` bigint(20) UNSIGNED NOT NULL,
  `id_mata_pelajaran_master` bigint(20) UNSIGNED NOT NULL,
  `semester` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mk_kurikulum` (`id_kurikulum`),
  KEY `fk_mk_master` (`id_mata_pelajaran_master`),
  CONSTRAINT `fk_mk_kurikulum` FOREIGN KEY (`id_kurikulum`) REFERENCES `kurikulum` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mk_master` FOREIGN KEY (`id_mata_pelajaran_master`) REFERENCES `mata_pelajaran_master` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. KELAS
-- ============================================
CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ro_program_kelas` bigint(20) UNSIGNED NOT NULL COMMENT 'FK ke reference_option (program_kelas)',
  `semester` int(11) NOT NULL DEFAULT 1,
  `id_jenjang_pendidikan` bigint(20) UNSIGNED NOT NULL,
  `id_tahun_akademik` bigint(20) UNSIGNED NOT NULL,
  `id_jurusan` bigint(20) UNSIGNED DEFAULT NULL,
  `status_aktif` enum('Y','N') DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kelas_program` (`ro_program_kelas`),
  KEY `fk_kelas_jenjang` (`id_jenjang_pendidikan`),
  KEY `fk_kelas_tahun` (`id_tahun_akademik`),
  KEY `fk_kelas_jurusan` (`id_jurusan`),
  CONSTRAINT `fk_kelas_program` FOREIGN KEY (`ro_program_kelas`) REFERENCES `reference_option` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kelas_jenjang` FOREIGN KEY (`id_jenjang_pendidikan`) REFERENCES `jenjang_pendidikan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kelas_tahun` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kelas_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. MATA PELAJARAN KELAS (Jadwal)
-- ============================================
CREATE TABLE `mata_pelajaran_kelas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_mata_pelajaran_kurikulum` bigint(20) UNSIGNED NOT NULL,
  `id_kelas` bigint(20) UNSIGNED NOT NULL,
  `id_dosen_data` bigint(20) UNSIGNED DEFAULT NULL,
  `ro_ruang_kelas` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (ruang_kelas)',
  `ro_pelaksanaan_kelas` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (pelaksanaan_kelas)',
  `id_pengawas` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke dosen_data',
  `jumlah` int(11) DEFAULT 0,
  `hari` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam` varchar(50) DEFAULT NULL,
  `uts` datetime DEFAULT NULL,
  `uas` datetime DEFAULT NULL,
  `tgl_uts` date DEFAULT NULL,
  `tgl_uas` date DEFAULT NULL,
  `status_uts` enum('Y','N') DEFAULT 'N',
  `status_uas` enum('Y','N') DEFAULT 'N',
  `ruang_uts` varchar(100) DEFAULT NULL,
  `ruang_uas` varchar(100) DEFAULT NULL,
  `link_kelas` text DEFAULT NULL,
  `passcode` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mpk_mk` (`id_mata_pelajaran_kurikulum`),
  KEY `fk_mpk_kelas` (`id_kelas`),
  KEY `fk_mpk_dosen` (`id_dosen_data`),
  KEY `fk_mpk_ruang` (`ro_ruang_kelas`),
  KEY `fk_mpk_pelaksanaan` (`ro_pelaksanaan_kelas`),
  KEY `fk_mpk_pengawas` (`id_pengawas`),
  CONSTRAINT `fk_mpk_mk` FOREIGN KEY (`id_mata_pelajaran_kurikulum`) REFERENCES `mata_pelajaran_kurikulum` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mpk_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mpk_dosen` FOREIGN KEY (`id_dosen_data`) REFERENCES `dosen_data` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mpk_pengawas` FOREIGN KEY (`id_pengawas`) REFERENCES `dosen_data` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mpk_ruang` FOREIGN KEY (`ro_ruang_kelas`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mpk_pelaksanaan` FOREIGN KEY (`ro_pelaksanaan_kelas`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. SISWA DATA
-- ============================================
CREATE TABLE `siswa_data` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `nomor_induk` varchar(50) DEFAULT NULL COMMENT 'NIM/NISN',
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `golongan_darah` varchar(5) DEFAULT NULL,
  `kota_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `nomor_rumah` varchar(50) DEFAULT NULL,
  `dusun` varchar(100) DEFAULT NULL,
  `rt` varchar(10) DEFAULT NULL,
  `rw` varchar(10) DEFAULT NULL,
  `desa` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kabupaten` varchar(100) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `tempat_domisili` varchar(255) DEFAULT NULL,
  `jenis_domisili` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `no_ktp` varchar(20) DEFAULT NULL,
  `no_kk` varchar(20) DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL COMMENT 'Bisa jadi ro_ jika perlu',
  `kewarganegaraan` varchar(50) DEFAULT 'Indonesia',
  `kode_negara` varchar(10) DEFAULT NULL,
  `status_pkawin` varchar(50) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `biaya_ditanggung` varchar(100) DEFAULT NULL,
  `transportasi` varchar(100) DEFAULT NULL,
  `status_asal_sekolah` varchar(100) DEFAULT NULL,
  `asal_slta` varchar(255) DEFAULT NULL,
  `jenis_slta` varchar(100) DEFAULT NULL,
  `kejuruan_slta` varchar(100) DEFAULT NULL,
  `alamat_lengkap_sekolah_asal` text DEFAULT NULL,
  `tahun_lulus_slta` year(4) DEFAULT NULL,
  `nomor_seri_ijazah_slta` varchar(100) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `anak_ke` int(11) DEFAULT NULL,
  `jumlah_saudara` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `penerima_kps` enum('Y','N') DEFAULT 'N',
  `no_kps` varchar(50) DEFAULT NULL,
  `kebutuhan_khusus` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nomor_induk` (`nomor_induk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. RIWAYAT PENDIDIKAN
-- ============================================
CREATE TABLE `riwayat_pendidikan` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_siswa_data` bigint(20) UNSIGNED NOT NULL,
  `id_jenjang_pendidikan` bigint(20) UNSIGNED NOT NULL,
  `id_jurusan` bigint(20) UNSIGNED NOT NULL,
  `ro_program_sekolah` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (program_sekolah)',
  `nomor_induk` varchar(50) DEFAULT NULL,
  `ro_status_siswa` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (status_siswa)',
  `angkatan` year(4) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `mulai_smt` int(11) DEFAULT NULL,
  `smt_aktif` int(11) DEFAULT NULL,
  `th_masuk` year(4) DEFAULT NULL,
  `dosen_wali` varchar(255) DEFAULT NULL,
  `no_seri_ijazah` varchar(100) DEFAULT NULL,
  `sks_diakui` int(11) DEFAULT NULL,
  `jalur_skripsi` varchar(100) DEFAULT NULL,
  `judul_skripsi` text DEFAULT NULL,
  `bln_awal_bimbingan` date DEFAULT NULL,
  `bln_akhir_bimbingan` date DEFAULT NULL,
  `sk_yudisium` varchar(100) DEFAULT NULL,
  `tgl_sk_yudisium` date DEFAULT NULL,
  `ipk` decimal(3,2) DEFAULT NULL,
  `nm_pt_asal` varchar(255) DEFAULT NULL,
  `nm_prodi_asal` varchar(255) DEFAULT NULL,
  `ro_jns_daftar` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (jns_pendaftaran)',
  `ro_jns_keluar` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK ke reference_option (jns_keluar)',
  `keluar_smt` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `pembiayaan` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_riwayat_siswa` (`id_siswa_data`),
  KEY `fk_riwayat_jenjang` (`id_jenjang_pendidikan`),
  KEY `fk_riwayat_jurusan` (`id_jurusan`),
  KEY `fk_riwayat_program` (`ro_program_sekolah`),
  KEY `fk_riwayat_status` (`ro_status_siswa`),
  KEY `fk_riwayat_jns_daftar` (`ro_jns_daftar`),
  KEY `fk_riwayat_jns_keluar` (`ro_jns_keluar`),
  CONSTRAINT `fk_riwayat_siswa` FOREIGN KEY (`id_siswa_data`) REFERENCES `siswa_data` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_riwayat_jenjang` FOREIGN KEY (`id_jenjang_pendidikan`) REFERENCES `jenjang_pendidikan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_riwayat_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_riwayat_program` FOREIGN KEY (`ro_program_sekolah`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_riwayat_status` FOREIGN KEY (`ro_status_siswa`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_riwayat_jns_daftar` FOREIGN KEY (`ro_jns_daftar`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_riwayat_jns_keluar` FOREIGN KEY (`ro_jns_keluar`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. AKADEMIK KRS
-- ============================================
CREATE TABLE `akademik_krs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_riwayat_pendidikan` bigint(20) UNSIGNED NOT NULL,
  `id_kelas` bigint(20) UNSIGNED NOT NULL,
  `semester` int(11) NOT NULL DEFAULT 1,
  `jumlah_sks` int(11) DEFAULT 0,
  `tgl_krs` date DEFAULT NULL,
  `kode_ta` varchar(50) DEFAULT NULL,
  `status_bayar` enum('Y','N') DEFAULT 'N',
  `syarat_uts` enum('Y','N') DEFAULT 'N',
  `syarat_krs` enum('Y','N') DEFAULT 'N',
  `kwitansi_krs` varchar(255) DEFAULT NULL,
  `status_aktif` enum('Y','N') DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_krs_riwayat` (`id_riwayat_pendidikan`),
  KEY `fk_krs_kelas` (`id_kelas`),
  CONSTRAINT `fk_krs_riwayat` FOREIGN KEY (`id_riwayat_pendidikan`) REFERENCES `riwayat_pendidikan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_krs_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 16. ABSENSI SISWA
-- ============================================
CREATE TABLE `absensi_siswa` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_krs` bigint(20) UNSIGNED NOT NULL,
  `id_mata_pelajaran_kelas` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') DEFAULT 'Hadir',
  `waktu_absen` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_absensi_krs` (`id_krs`),
  KEY `fk_absensi_mpk` (`id_mata_pelajaran_kelas`),
  CONSTRAINT `fk_absensi_krs` FOREIGN KEY (`id_krs`) REFERENCES `akademik_krs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_absensi_mpk` FOREIGN KEY (`id_mata_pelajaran_kelas`) REFERENCES `mata_pelajaran_kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 17. SISWA DATA LJK (Nilai)
-- ============================================
CREATE TABLE `siswa_data_ljk` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_akademik_krs` bigint(20) UNSIGNED NOT NULL,
  `id_mata_pelajaran_kelas` bigint(20) UNSIGNED NOT NULL,
  `nilai` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ljk_krs` (`id_akademik_krs`),
  KEY `fk_ljk_mpk` (`id_mata_pelajaran_kelas`),
  CONSTRAINT `fk_ljk_krs` FOREIGN KEY (`id_akademik_krs`) REFERENCES `akademik_krs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ljk_mpk` FOREIGN KEY (`id_mata_pelajaran_kelas`) REFERENCES `mata_pelajaran_kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 18. USERS (Optional - untuk autentikasi)
-- ============================================
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 19. SISWA DATA PENDAFTAR (Optional)
-- ============================================
CREATE TABLE `siswa_data_pendaftar` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_siswa_data` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pendaftar_siswa` (`id_siswa_data`),
  CONSTRAINT `fk_pendaftar_siswa` FOREIGN KEY (`id_siswa_data`) REFERENCES `siswa_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 20. SISWA DATA ORANG TUA (Optional)
-- ============================================
CREATE TABLE `siswa_data_orang_tua` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_siswa_data` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ortu_siswa` (`id_siswa_data`),
  CONSTRAINT `fk_ortu_siswa` FOREIGN KEY (`id_siswa_data`) REFERENCES `siswa_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- 1. Reference Options
INSERT INTO `reference_option` (`nama_grup`, `kode`, `nilai`, `status`) VALUES
-- Program Kelas
('program_kelas', 'A', 'Reguler Pagi', 'Y'),
('program_kelas', 'B', 'Reguler Sore', 'Y'),
('program_kelas', 'C', 'Karyawan', 'Y'),
-- Ruang Kelas
('ruang_kelas', 'R101', 'Ruang 101', 'Y'),
('ruang_kelas', 'R102', 'Ruang 102', 'Y'),
('ruang_kelas', 'R103', 'Ruang 103', 'Y'),
('ruang_kelas', 'R201', 'Ruang 201', 'Y'),
('ruang_kelas', 'R202', 'Ruang 202', 'Y'),
('ruang_kelas', 'LAB1', 'Lab Komputer 1', 'Y'),
-- Status Siswa
('status_siswa', 'A', 'Aktif', 'Y'),
('status_siswa', 'C', 'Cuti', 'Y'),
('status_siswa', 'L', 'Lulus', 'Y'),
('status_siswa', 'K', 'Keluar', 'Y'),
('status_siswa', 'D', 'DO', 'Y'),
-- Agama
('agama', 'ISL', 'Islam', 'Y'),
('agama', 'KRS', 'Kristen', 'Y'),
('agama', 'KTL', 'Katolik', 'Y'),
('agama', 'HND', 'Hindu', 'Y'),
('agama', 'BDH', 'Buddha', 'Y'),
('agama', 'KHG', 'Konghucu', 'Y'),
-- Status Dosen
('status_dosen', 'TT', 'Tetap', 'Y'),
('status_dosen', 'TDK', 'Tidak Tetap', 'Y'),
-- Pangkat Golongan
('pangkat', 'III/a', 'Penata Muda', 'Y'),
('pangkat', 'III/b', 'Penata Muda Tingkat I', 'Y'),
('pangkat', 'III/c', 'Penata', 'Y'),
('pangkat', 'III/d', 'Penata Tingkat I', 'Y'),
('pangkat', 'IV/a', 'Pembina', 'Y'),
-- Jabatan Fungsional
('jabatan_fungsional', 'AA', 'Asisten Ahli', 'Y'),
('jabatan_fungsional', 'L', 'Lektor', 'Y'),
('jabatan_fungsional', 'LK', 'Lektor Kepala', 'Y'),
('jabatan_fungsional', 'GB', 'Guru Besar', 'Y');

-- 2. Jenjang Pendidikan
INSERT INTO `jenjang_pendidikan` (`nama`, `deskripsi`) VALUES
('S1', 'Sarjana Strata 1'),
('S2', 'Magister Strata 2'),
('S3', 'Doktor Strata 3'),
('D3', 'Diploma 3');

-- 3. Tahun Akademik
INSERT INTO `tahun_akademik` (`nama`, `periode`, `status`) VALUES
('2024/2025', 'Ganjil', 'N'),
('2024/2025', 'Genap', 'N'),
('2025/2026', 'Ganjil', 'Y'),
('2025/2026', 'Genap', 'N');

-- 4. Fakultas
INSERT INTO `fakultas` (`nama`) VALUES
('Fakultas Teknik'),
('Fakultas Ekonomi dan Bisnis'),
('Fakultas Ilmu Sosial dan Politik');

-- 5. Jurusan (contoh untuk Fakultas Teknik)
INSERT INTO `jurusan` (`nama`, `id_fakultas`) VALUES
('Teknik Informatika', 1),
('Sistem Informasi', 1),
('Teknik Elektro', 1);

-- ============================================
-- CATATAN PENTING:
-- ============================================
-- 1. Semua field yang menggunakan ro_ adalah foreign key ke reference_option
-- 2. Pastikan data reference_option di-insert terlebih dahulu
-- 3. Gunakan nama_grup untuk mengelompokkan reference option
-- 4. Status enum menggunakan 'Y' dan 'N' untuk konsistensi
-- 5. Semua tabel menggunakan InnoDB untuk mendukung foreign key
-- 6. Charset utf8mb4 untuk mendukung emoji dan karakter khusus
