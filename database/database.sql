-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 8.4.3 - MySQL Community Server - GPL
-- OS Server:                    Win64
-- HeidiSQL Versi:               12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk siakad
CREATE DATABASE IF NOT EXISTS `siakad` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `siakad`;

-- membuang struktur untuk table siakad.absensi_siswa
CREATE TABLE IF NOT EXISTS `absensi_siswa` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_krs` bigint unsigned NOT NULL,
  `id_mata_pelajaran_kelas` bigint unsigned NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') COLLATE utf8mb4_unicode_ci DEFAULT 'Hadir',
  `waktu_absen` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_absensi_krs` (`id_krs`),
  KEY `fk_absensi_mpk` (`id_mata_pelajaran_kelas`),
  CONSTRAINT `fk_absensi_krs` FOREIGN KEY (`id_krs`) REFERENCES `akademik_krs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_absensi_mpk` FOREIGN KEY (`id_mata_pelajaran_kelas`) REFERENCES `mata_pelajaran_kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.akademik_krs
CREATE TABLE IF NOT EXISTS `akademik_krs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_riwayat_pendidikan` bigint unsigned NOT NULL,
  `ro_program_kelas` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_sks` int DEFAULT '0',
  `tgl_krs` date DEFAULT NULL,
  `kode_tahun` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_bayar` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `syarat_uts` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `syarat_uas` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `syarat_krs` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `kwitansi_krs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `berkas_lain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aktif` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_krs_riwayat` (`id_riwayat_pendidikan`),
  CONSTRAINT `fk_krs_riwayat` FOREIGN KEY (`id_riwayat_pendidikan`) REFERENCES `riwayat_pendidikan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_bukus
CREATE TABLE IF NOT EXISTS `dosen_bukus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_dosen` bigint unsigned DEFAULT NULL,
  `id_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul_buku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_buku` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isbn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_isbn` text COLLATE utf8mb4_unicode_ci,
  `penerbit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dosen_bukus_id_dosen_index` (`id_dosen`),
  KEY `dosen_bukus_id_staff_index` (`id_staff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_data
CREATE TABLE IF NOT EXISTS `dosen_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `foto_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NIPDN` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NIY` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gelar_depan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gelar_belakang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ro_pangkat_gol` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (pangkat)',
  `ro_jabatan` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (jabatan_fungsional)',
  `id_jurusan` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ibu_kandung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kewarganegaraan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Alamat` text COLLATE utf8mb4_unicode_ci,
  `status_kawin` enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ro_status_dosen` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (status_dosen)',
  `ro_agama` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (agama)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dosen_jurusan` (`id_jurusan`),
  KEY `fk_dosen_pangkat` (`ro_pangkat_gol`),
  KEY `fk_dosen_jabatan` (`ro_jabatan`),
  KEY `fk_dosen_status` (`ro_status_dosen`),
  KEY `fk_dosen_agama` (`ro_agama`),
  KEY `dosen_data_user_id_foreign` (`user_id`),
  KEY `dosen_data_id_staff_index` (`id_staff`),
  CONSTRAINT `dosen_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dosen_agama` FOREIGN KEY (`ro_agama`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_jabatan` FOREIGN KEY (`ro_jabatan`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_pangkat` FOREIGN KEY (`ro_pangkat_gol`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dosen_status` FOREIGN KEY (`ro_status_dosen`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_dokumen
CREATE TABLE IF NOT EXISTS `dosen_dokumen` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_dosen` bigint unsigned NOT NULL,
  `id_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi_file` text COLLATE utf8mb4_unicode_ci,
  `file_size` int unsigned DEFAULT NULL,
  `file_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe_dokumen` enum('materi','tugas','rpp','silabus','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT 'lainnya',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_dokumen_dosen_dosen` (`id_dosen`),
  KEY `dosen_dokumen_id_staff_index` (`id_staff`),
  CONSTRAINT `fk_dokumen_dosen_dosen` FOREIGN KEY (`id_dosen`) REFERENCES `dosen_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_jurnal_pengajaran
CREATE TABLE IF NOT EXISTS `dosen_jurnal_pengajaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Tugas','Materi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Materi',
  `id_mata_pelajaran_kelas` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deadline` date DEFAULT NULL,
  `status_akses` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_jurnal_mata_pelajaran_kelas` (`id_mata_pelajaran_kelas`),
  CONSTRAINT `fk_jurnal_mata_pelajaran_kelas` FOREIGN KEY (`id_mata_pelajaran_kelas`) REFERENCES `mata_pelajaran_kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_penelitians
CREATE TABLE IF NOT EXISTS `dosen_penelitians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_dosen` bigint unsigned DEFAULT NULL,
  `id_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul_penelitian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `th_penelitian` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dana_penelitian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tingkat_penelitian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_file` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dosen_penelitians_id_dosen_index` (`id_dosen`),
  KEY `dosen_penelitians_id_staff_index` (`id_staff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_pengabdians
CREATE TABLE IF NOT EXISTS `dosen_pengabdians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_dosen` bigint unsigned DEFAULT NULL,
  `id_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul_pengabdian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_pengabdian` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dana_pengabdian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tingkat_pengabdian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_file` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dosen_pengabdians_id_dosen_index` (`id_dosen`),
  KEY `dosen_pengabdians_id_staff_index` (`id_staff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_penghargaans
CREATE TABLE IF NOT EXISTS `dosen_penghargaans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_dosen` bigint unsigned DEFAULT NULL,
  `id_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul_penghargaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_penghargaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_penghargaan` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tingkat_penghargaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_file` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dosen_penghargaans_id_dosen_index` (`id_dosen`),
  KEY `dosen_penghargaans_id_staff_index` (`id_staff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.dosen_riwayat_pendidikans
CREATE TABLE IF NOT EXISTS `dosen_riwayat_pendidikans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_dosen` bigint unsigned DEFAULT NULL,
  `id_staff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenjang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pendidikan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gelar_pendidikan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `th_lulus` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dosen_riwayat_pendidikans_id_dosen_index` (`id_dosen`),
  KEY `dosen_riwayat_pendidikans_id_staff_index` (`id_staff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.exports
CREATE TABLE IF NOT EXISTS `exports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exporter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exports_user_id_foreign` (`user_id`),
  CONSTRAINT `exports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.failed_import_rows
CREATE TABLE IF NOT EXISTS `failed_import_rows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data` json NOT NULL,
  `import_id` bigint unsigned NOT NULL,
  `validation_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_import_rows_import_id_foreign` (`import_id`),
  CONSTRAINT `failed_import_rows_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.fakultas
CREATE TABLE IF NOT EXISTS `fakultas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.imports
CREATE TABLE IF NOT EXISTS `imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imports_user_id_foreign` (`user_id`),
  CONSTRAINT `imports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.jurnal_dokumen
CREATE TABLE IF NOT EXISTS `jurnal_dokumen` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_jurnal` bigint unsigned NOT NULL,
  `id_dokumen` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_jurnal_dokumen` (`id_jurnal`,`id_dokumen`),
  KEY `idx_jurnal_dokumen_jurnal` (`id_jurnal`),
  KEY `idx_jurnal_dokumen_dokumen` (`id_dokumen`),
  CONSTRAINT `fk_jurnal_dokumen_dokumen` FOREIGN KEY (`id_dokumen`) REFERENCES `dosen_dokumen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jurnal_dokumen_jurnal` FOREIGN KEY (`id_jurnal`) REFERENCES `dosen_jurnal_pengajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.jurusan
CREATE TABLE IF NOT EXISTS `jurusan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_fakultas` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_jurusan_fakultas` (`id_fakultas`),
  CONSTRAINT `fk_jurusan_fakultas` FOREIGN KEY (`id_fakultas`) REFERENCES `fakultas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ro_program_kelas` bigint unsigned NOT NULL COMMENT 'FK ke reference_option (program_kelas)',
  `semester` int NOT NULL DEFAULT '1',
  `total` int DEFAULT '20',
  `id_tahun_akademik` bigint unsigned NOT NULL,
  `id_jurusan` bigint unsigned DEFAULT NULL,
  `status_aktif` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kelas_program` (`ro_program_kelas`),
  KEY `fk_kelas_tahun` (`id_tahun_akademik`),
  KEY `fk_kelas_jurusan` (`id_jurusan`),
  CONSTRAINT `fk_kelas_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_kelas_program` FOREIGN KEY (`ro_program_kelas`) REFERENCES `reference_option` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kelas_tahun` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.krs_chats
CREATE TABLE IF NOT EXISTS `krs_chats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_dosen` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `krs_chats_id_dosen_index` (`id_dosen`),
  KEY `krs_chats_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.kurikulum
CREATE TABLE IF NOT EXISTS `kurikulum` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jurusan` bigint unsigned NOT NULL,
  `id_tahun_akademik` bigint unsigned NOT NULL,
  `status_aktif` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kurikulum_jurusan` (`id_jurusan`),
  KEY `fk_kurikulum_tahun` (`id_tahun_akademik`),
  CONSTRAINT `fk_kurikulum_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kurikulum_tahun` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_authors
CREATE TABLE IF NOT EXISTS `library_authors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_books
CREATE TABLE IF NOT EXISTS `library_books` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isbn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `library_author_id` bigint unsigned DEFAULT NULL,
  `library_publisher_id` bigint unsigned DEFAULT NULL,
  `library_category_id` bigint unsigned DEFAULT NULL,
  `year` int DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `total_borrows` int NOT NULL DEFAULT '0',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `library_books_isbn_unique` (`isbn`),
  KEY `library_books_library_author_id_foreign` (`library_author_id`),
  KEY `library_books_library_publisher_id_foreign` (`library_publisher_id`),
  KEY `library_books_library_category_id_foreign` (`library_category_id`),
  CONSTRAINT `library_books_library_author_id_foreign` FOREIGN KEY (`library_author_id`) REFERENCES `library_authors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `library_books_library_category_id_foreign` FOREIGN KEY (`library_category_id`) REFERENCES `library_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `library_books_library_publisher_id_foreign` FOREIGN KEY (`library_publisher_id`) REFERENCES `library_publishers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_categories
CREATE TABLE IF NOT EXISTS `library_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_loans
CREATE TABLE IF NOT EXISTS `library_loans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `riwayat_pendidikan_id` bigint unsigned NOT NULL,
  `borrowed_at` datetime NOT NULL,
  `due_at` datetime NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `status` enum('borrowed','returned','overdue','lost') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrowed',
  `fine_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `staff_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_loans_riwayat_pendidikan_id_foreign` (`riwayat_pendidikan_id`),
  KEY `library_loans_staff_id_foreign` (`staff_id`),
  CONSTRAINT `library_loans_riwayat_pendidikan_id_foreign` FOREIGN KEY (`riwayat_pendidikan_id`) REFERENCES `riwayat_pendidikan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `library_loans_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_loan_details
CREATE TABLE IF NOT EXISTS `library_loan_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `library_loan_id` bigint unsigned NOT NULL,
  `library_book_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_loan_details_library_loan_id_foreign` (`library_loan_id`),
  KEY `library_loan_details_library_book_id_foreign` (`library_book_id`),
  CONSTRAINT `library_loan_details_library_book_id_foreign` FOREIGN KEY (`library_book_id`) REFERENCES `library_books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `library_loan_details_library_loan_id_foreign` FOREIGN KEY (`library_loan_id`) REFERENCES `library_loans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_procurements
CREATE TABLE IF NOT EXISTS `library_procurements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `procurement_date` datetime NOT NULL,
  `staff_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `library_procurements_reference_no_unique` (`reference_no`),
  KEY `library_procurements_staff_id_foreign` (`staff_id`),
  CONSTRAINT `library_procurements_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_procurement_details
CREATE TABLE IF NOT EXISTS `library_procurement_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `library_procurement_id` bigint unsigned NOT NULL,
  `library_book_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_procurement_details_library_procurement_id_foreign` (`library_procurement_id`),
  KEY `library_procurement_details_library_book_id_foreign` (`library_book_id`),
  CONSTRAINT `library_procurement_details_library_book_id_foreign` FOREIGN KEY (`library_book_id`) REFERENCES `library_books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `library_procurement_details_library_procurement_id_foreign` FOREIGN KEY (`library_procurement_id`) REFERENCES `library_procurements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_publishers
CREATE TABLE IF NOT EXISTS `library_publishers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.library_visits
CREATE TABLE IF NOT EXISTS `library_visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `riwayat_pendidikan_id` bigint unsigned NOT NULL,
  `visited_at` datetime NOT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `library_visits_riwayat_pendidikan_id_foreign` (`riwayat_pendidikan_id`),
  CONSTRAINT `library_visits_riwayat_pendidikan_id_foreign` FOREIGN KEY (`riwayat_pendidikan_id`) REFERENCES `riwayat_pendidikan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.mata_pelajaran_kelas
CREATE TABLE IF NOT EXISTS `mata_pelajaran_kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_mata_pelajaran_kurikulum` bigint unsigned NOT NULL,
  `id_kelas` bigint unsigned NOT NULL,
  `id_dosen_data` bigint unsigned DEFAULT NULL,
  `uts` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uas` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ro_ruang_kelas` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (ruang_kelas)',
  `ro_pelaksanaan_kelas` bigint unsigned DEFAULT NULL,
  `id_pengawas` bigint unsigned DEFAULT NULL,
  `jumlah` int DEFAULT '0',
  `hari` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `soal_uas` text COLLATE utf8mb4_unicode_ci,
  `soal_uts` text COLLATE utf8mb4_unicode_ci,
  `jam` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_uts` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `status_uas` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `ruang_uts` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruang_uas` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_kelas` text COLLATE utf8mb4_unicode_ci,
  `passcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ctt_soal_uts` text COLLATE utf8mb4_unicode_ci,
  `ctt_soal_uas` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_mpk_mk` (`id_mata_pelajaran_kurikulum`),
  KEY `fk_mpk_kelas` (`id_kelas`),
  KEY `fk_mpk_dosen` (`id_dosen_data`),
  KEY `fk_mpk_ruang` (`ro_ruang_kelas`),
  CONSTRAINT `fk_mpk_dosen` FOREIGN KEY (`id_dosen_data`) REFERENCES `dosen_data` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mpk_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mpk_mk` FOREIGN KEY (`id_mata_pelajaran_kurikulum`) REFERENCES `mata_pelajaran_kurikulum` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mpk_ruang` FOREIGN KEY (`ro_ruang_kelas`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.mata_pelajaran_kurikulum
CREATE TABLE IF NOT EXISTS `mata_pelajaran_kurikulum` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_kurikulum` bigint unsigned NOT NULL,
  `id_mata_pelajaran_master` bigint unsigned NOT NULL,
  `semester` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mk_kurikulum` (`id_kurikulum`),
  KEY `fk_mk_master` (`id_mata_pelajaran_master`),
  CONSTRAINT `fk_mk_kurikulum` FOREIGN KEY (`id_kurikulum`) REFERENCES `kurikulum` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mk_master` FOREIGN KEY (`id_mata_pelajaran_master`) REFERENCES `mata_pelajaran_master` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.mata_pelajaran_master
CREATE TABLE IF NOT EXISTS `mata_pelajaran_master` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_feeder` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jurusan` bigint unsigned NOT NULL,
  `bobot` int DEFAULT '3' COMMENT 'SKS',
  `jenis` enum('wajib','peminatan') COLLATE utf8mb4_unicode_ci DEFAULT 'wajib',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_matkul_master_jurusan` (`id_jurusan`),
  CONSTRAINT `fk_matkul_master_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.pekan_ujian
CREATE TABLE IF NOT EXISTS `pekan_ujian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_tahun_akademik` bigint unsigned NOT NULL,
  `jenis_ujian` enum('UTS','UAS') DEFAULT NULL,
  `status_akses` enum('Y','N') DEFAULT NULL,
  `status_bayar` enum('Y','N') DEFAULT NULL,
  `status_ujian` enum('Y','N') DEFAULT NULL,
  `informasi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_tahun_akademik` (`id_tahun_akademik`),
  CONSTRAINT `FK_pekan_ujian_tahun_akademik` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.pengajuan_surats
CREATE TABLE IF NOT EXISTS `pengajuan_surats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_riwayat_pendidikan` bigint unsigned NOT NULL,
  `id_tahun_akademik` bigint unsigned NOT NULL,
  `jenis_surat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keperluan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `file_pendukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_hasil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_surats_id_riwayat_pendidikan_foreign` (`id_riwayat_pendidikan`),
  KEY `pengajuan_surats_id_tahun_akademik_foreign` (`id_tahun_akademik`),
  CONSTRAINT `pengajuan_surats_id_riwayat_pendidikan_foreign` FOREIGN KEY (`id_riwayat_pendidikan`) REFERENCES `riwayat_pendidikan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengajuan_surats_id_tahun_akademik_foreign` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.pengaturan_pendaftaran
CREATE TABLE IF NOT EXISTS `pengaturan_pendaftaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `biaya_reguler` decimal(15,2) NOT NULL DEFAULT '100000.00',
  `biaya_beasiswa` decimal(15,2) NOT NULL DEFAULT '50000.00',
  `foto_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi_pendaftaran` text COLLATE utf8mb4_unicode_ci,
  `status_pendaftaran` tinyint(1) NOT NULL DEFAULT '1',
  `tanggal_buka` datetime DEFAULT NULL,
  `tanggal_tutup` datetime DEFAULT NULL,
  `id_tahun_akademik` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pengumuman` text COLLATE utf8mb4_unicode_ci,
  `kontak_admin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_admin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `brosur_pendaftaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gelombang_1_buka` date DEFAULT NULL,
  `gelombang_1_tutup` date DEFAULT NULL,
  `gelombang_1_aktif` tinyint(1) NOT NULL DEFAULT '0',
  `gelombang_2_buka` date DEFAULT NULL,
  `gelombang_2_tutup` date DEFAULT NULL,
  `gelombang_2_aktif` tinyint(1) NOT NULL DEFAULT '0',
  `gelombang_3_buka` date DEFAULT NULL,
  `gelombang_3_tutup` date DEFAULT NULL,
  `gelombang_3_aktif` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.periode_wisudas
CREATE TABLE IF NOT EXISTS `periode_wisudas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun` year NOT NULL,
  `periode_ke` int NOT NULL,
  `kuota` int NOT NULL DEFAULT '800',
  `pendaftar_count` int NOT NULL DEFAULT '0',
  `status` enum('Buka','Tutup','Belum Dibuka') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Dibuka',
  `tanggal_pelaksanaan` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=492 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.referal_codes
CREATE TABLE IF NOT EXISTS `referal_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `type` enum('internal','eksternal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'internal',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referal_codes_kode_unique` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.reference_option
CREATE TABLE IF NOT EXISTS `reference_option` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_grup` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama grup option (program_kelas, ruang_kelas, status_siswa, dll)',
  `kode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kode singkat',
  `nilai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nilai/Label yang ditampilkan',
  `status` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'Y' COMMENT 'Status aktif',
  `deskripsi` text COLLATE utf8mb4_unicode_ci COMMENT 'Deskripsi tambahan',
  `is_aktif` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_nama_grup` (`nama_grup`),
  KEY `idx_kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.riwayat_pendidikan
CREATE TABLE IF NOT EXISTS `riwayat_pendidikan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa_data` bigint unsigned NOT NULL,
  `id_jurusan` bigint unsigned NOT NULL,
  `ro_program_sekolah` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (program_sekolah)',
  `ro_status_siswa` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (status_siswa)',
  `id_wali_dosen` bigint unsigned DEFAULT NULL,
  `nomor_induk` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `foto_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mulai_smt` int DEFAULT NULL,
  `smt_aktif` int DEFAULT NULL,
  `dosen_wali` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_seri_ijazah` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sks_diakui` int DEFAULT NULL,
  `jalur_skripsi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judul_skripsi` text COLLATE utf8mb4_unicode_ci,
  `bln_awal_bimbingan` date DEFAULT NULL,
  `bln_akhir_bimbingan` date DEFAULT NULL,
  `sk_yudisium` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_sk_yudisium` date DEFAULT NULL,
  `ipk` decimal(3,2) DEFAULT NULL,
  `nm_pt_asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nm_prodi_asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ro_jns_daftar` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (jns_pendaftaran)',
  `ro_jns_keluar` bigint unsigned DEFAULT NULL COMMENT 'FK ke reference_option (jns_keluar)',
  `keluar_smt` int DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `pembiayaan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_tahun_akademik` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_riwayat_siswa` (`id_siswa_data`),
  KEY `fk_riwayat_jurusan` (`id_jurusan`),
  KEY `fk_riwayat_program` (`ro_program_sekolah`),
  KEY `fk_riwayat_status` (`ro_status_siswa`),
  KEY `fk_riwayat_jns_daftar` (`ro_jns_daftar`),
  KEY `fk_riwayat_jns_keluar` (`ro_jns_keluar`),
  KEY `id_wali_dosen` (`id_wali_dosen`),
  KEY `riwayat_pendidikan_id_tahun_akademik_foreign` (`id_tahun_akademik`),
  CONSTRAINT `fk_riwayat_jns_daftar` FOREIGN KEY (`ro_jns_daftar`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_riwayat_jns_keluar` FOREIGN KEY (`ro_jns_keluar`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_riwayat_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_riwayat_program` FOREIGN KEY (`ro_program_sekolah`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_riwayat_siswa` FOREIGN KEY (`id_siswa_data`) REFERENCES `siswa_data` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_riwayat_status` FOREIGN KEY (`ro_status_siswa`) REFERENCES `reference_option` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_riwayat_wali` FOREIGN KEY (`id_wali_dosen`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `riwayat_pendidikan_id_tahun_akademik_foreign` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.siswa_data
CREATE TABLE IF NOT EXISTS `siswa_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_induk` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NIM/NISN',
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `golongan_darah` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota_lahir` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `nomor_rumah` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dusun` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desa` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_domisili` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_domisili` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_ktp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_kk` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agama` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bisa jadi ro_ jika perlu',
  `kewarganegaraan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Indonesia',
  `kode_negara` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pkawin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biaya_ditanggung` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transportasi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_asal_sekolah` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asal_slta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_slta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kejuruan_slta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_lengkap_sekolah_asal` text COLLATE utf8mb4_unicode_ci,
  `tahun_lulus_slta` year DEFAULT NULL,
  `nomor_seri_ijazah_slta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nisn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anak_ke` int DEFAULT NULL,
  `jumlah_saudara` int DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penerima_kps` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `no_kps` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kebutuhan_khusus` text COLLATE utf8mb4_unicode_ci,
  `status_siswa` enum('aktif','tidak aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'tidak aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nomor_induk` (`nomor_induk`),
  KEY `fk_siswa_user` (`user_id`),
  CONSTRAINT `fk_siswa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.siswa_data_ljk
CREATE TABLE IF NOT EXISTS `siswa_data_ljk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_akademik_krs` bigint unsigned NOT NULL,
  `id_mata_pelajaran_kelas` bigint unsigned NOT NULL,
  `nilai` decimal(5,2) DEFAULT NULL,
  `ljk_simulasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ljk_uas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `artikel_uas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tgl_upload_ljk_uas` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_upload_artikel_uas` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ljk_uts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `artikel_uts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tgl_upload_ljk_uts` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_upload_artikel_uts` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tugas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ljk_tugas_1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ljk_tugas_2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ljk_tugas_3` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tgl_upload_tugas` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nilai_UTS` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nilai_TGS_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nilai_TGS_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nilai_TGS_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nilai_UAS` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nilai_Performance` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nilai_Akhir` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Nilai_Huruf` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Status_Nilai` enum('L','TL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'TL',
  `Rekom_Nilai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ket` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transfer` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cekal_kuliah` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `ctt_uts` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ctt_uas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ctt_tugas_1` text COLLATE utf8mb4_unicode_ci,
  `ctt_tugas_2` text COLLATE utf8mb4_unicode_ci,
  `ctt_tugas_3` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `ljk_tugas_4` json DEFAULT NULL,
  `ctt_tugas_4` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_4` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_5` json DEFAULT NULL,
  `ctt_tugas_5` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_5` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_6` json DEFAULT NULL,
  `ctt_tugas_6` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_6` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_7` json DEFAULT NULL,
  `ctt_tugas_7` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_7` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_8` json DEFAULT NULL,
  `ctt_tugas_8` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_8` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_9` json DEFAULT NULL,
  `ctt_tugas_9` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_9` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_10` json DEFAULT NULL,
  `ctt_tugas_10` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_10` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_11` json DEFAULT NULL,
  `ctt_tugas_11` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_11` decimal(5,2) DEFAULT NULL,
  `ljk_tugas_12` json DEFAULT NULL,
  `ctt_tugas_12` text COLLATE utf8mb4_unicode_ci,
  `Nilai_TGS_12` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ljk_krs` (`id_akademik_krs`),
  KEY `fk_ljk_mpk` (`id_mata_pelajaran_kelas`),
  CONSTRAINT `fk_ljk_krs` FOREIGN KEY (`id_akademik_krs`) REFERENCES `akademik_krs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ljk_mpk` FOREIGN KEY (`id_mata_pelajaran_kelas`) REFERENCES `mata_pelajaran_kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.siswa_data_orang_tua
CREATE TABLE IF NOT EXISTS `siswa_data_orang_tua` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa_data` bigint unsigned NOT NULL,
  `Nama_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `Tempat_Lhr_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tgl_Lhr_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Bln_Lhr_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Thn_Lhr_ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Agama_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Gol_Darah_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Pendidikan_Terakhir_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Pekerjaan_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Penghasilan_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kebutuhan_Khusus_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nomor_KTP_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Alamat_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `No_Rmh_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dusun_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RT_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RW_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Desa_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kec_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kab_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kode_Pos_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Prov_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kewarganegaraan_Ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nama_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tempat_Lhr_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tgl_Lhr_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Bln_Lhr_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Thn_Lhr_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Agama_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Gol_Darah_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Pendidikan_Terakhir_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Pekerjaan_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Penghasilan_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kebutuhan_Khusus_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nomor_KTP_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Alamat_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `No_Rmh_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dusun_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RT_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RW_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Desa_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kec_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kab_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kode_Pos_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Prov_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Kewarganegaraan_Ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `No_HP_ayah` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `No_HP_ibu` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ortu_siswa` (`id_siswa_data`),
  CONSTRAINT `fk_ortu_siswa` FOREIGN KEY (`id_siswa_data`) REFERENCES `siswa_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.siswa_data_pendaftar
CREATE TABLE IF NOT EXISTS `siswa_data_pendaftar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa_data` bigint unsigned NOT NULL,
  `Nama_Lengkap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `No_Pendaftaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tgl_Daftar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ro_program_sekolah` bigint unsigned DEFAULT NULL,
  `Kelas_Program_Kuliah` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_jurusan` bigint unsigned DEFAULT NULL,
  `Prodi_Pilihan_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Prodi_Pilihan_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Jalur_PMB` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Bukti_Jalur_PMB` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Jenis_Pembiayaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Bukti_Jenis_Pembiayaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NIMKO_Asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Prodi_Asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PT_Asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Jml_SKS_Asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IPK_Asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Semester_Asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Pengantar_Mutasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Transkip_Asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Legalisir_Ijazah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Legalisir_SKHU` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Copy_KTP` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Foto_BW_3x3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Foto_BW_3x4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Foto_Warna_5x6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `File_Foto_Berwarna` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nama_File_Foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tgl_Tes_Tulis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Agama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Umum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Psiko` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Jumlah_Tes_Tulis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Rerata_Tes_Tulis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tgl_Tes_Lisan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Potensi_Akademik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Baca_al_Quran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Baca_Kitab_Kuning` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Jumlah_Tes_Lisan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `N_Rearata_Tes_Lisan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Jumlah_Nilai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Rata_Rata` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Rekomendasi_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Rekomendasi_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `No_SK_Kelulusan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tgl_SK_Kelulusan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Diterima_di_Prodi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Biaya_Pendaftaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Bukti_Biaya_Daftar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verifikator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_valid` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `Status_Pendaftaran` enum('Y','N','B') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'B',
  `Status_Kelulusan_Seleksi` enum('Y','N','B') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'B',
  `id_referal_code` bigint unsigned DEFAULT NULL,
  `id_tahun_akademik` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pendaftar_siswa` (`id_siswa_data`),
  KEY `id_jurusan` (`id_jurusan`),
  KEY `siswapendafta_programkelas` (`ro_program_sekolah`),
  KEY `siswa_data_pendaftar_id_referal_code_foreign` (`id_referal_code`),
  KEY `siswa_data_pendaftar_id_tahun_akademik_foreign` (`id_tahun_akademik`),
  CONSTRAINT `fk_pendaftar_siswa` FOREIGN KEY (`id_siswa_data`) REFERENCES `siswa_data` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_data_pendaftar_id_referal_code_foreign` FOREIGN KEY (`id_referal_code`) REFERENCES `referal_codes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswa_data_pendaftar_id_tahun_akademik_foreign` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswapendafta_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id`),
  CONSTRAINT `siswapendafta_programkelas` FOREIGN KEY (`ro_program_sekolah`) REFERENCES `reference_option` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.siswa_seleksi_pendaftar
CREATE TABLE IF NOT EXISTS `siswa_seleksi_pendaftar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa_data_pendaftar` bigint unsigned NOT NULL,
  `nama_seleksi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_seleksi` datetime DEFAULT NULL,
  `deskripsi_seleksi` text COLLATE utf8mb4_unicode_ci,
  `file_persyaratan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_jawaban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_seleksi` enum('B','Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B' COMMENT 'B: Pending, Y: Lulus/Sesuai, N: Tidak Lulus',
  `keterangan_admin` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_seleksi_pendaftar_id_siswa_data_pendaftar_index` (`id_siswa_data_pendaftar`),
  CONSTRAINT `siswa_seleksi_pendaftar_id_siswa_data_pendaftar_foreign` FOREIGN KEY (`id_siswa_data_pendaftar`) REFERENCES `siswa_data_pendaftar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.tahun_akademik
CREATE TABLE IF NOT EXISTS `tahun_akademik` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contoh: 2024/2025',
  `periode` enum('Ganjil','Genap') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Y','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N' COMMENT 'Y = Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.ta_pengajuan_judul
CREATE TABLE IF NOT EXISTS `ta_pengajuan_judul` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_tahun_akademik` bigint unsigned NOT NULL,
  `id_riwayat_pendidikan` bigint unsigned NOT NULL,
  `judul` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abstrak` text COLLATE utf8mb4_unicode_ci,
  `tgl_pengajuan` date NOT NULL,
  `tgl_ujian` date DEFAULT NULL,
  `ruangan_ujian` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_acc_judul` date DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_dosen_pembimbing_1` bigint unsigned DEFAULT NULL,
  `id_dosen_pembimbing_2` bigint unsigned DEFAULT NULL,
  `id_dosen_pembimbing_3` bigint unsigned DEFAULT NULL,
  `status_dosen_1` enum('pending','setuju','ditolak','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status_dosen_2` enum('pending','setuju','ditolak','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status_dosen_3` enum('pending','setuju','ditolak','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `nilai_dosen_1` decimal(5,2) DEFAULT NULL,
  `nilai_dosen_2` decimal(5,2) DEFAULT NULL,
  `nilai_dosen_3` decimal(5,2) DEFAULT NULL,
  `file_revisi_dosen_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_revisi_dosen_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_revisi_dosen_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ctt_revisi_dosen_1` text COLLATE utf8mb4_unicode_ci,
  `ctt_revisi_dosen_2` text COLLATE utf8mb4_unicode_ci,
  `ctt_revisi_dosen_3` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','disetujui','ditolak','revisi','selesai') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_tahun_akademik` (`id_tahun_akademik`),
  KEY `id_dosen_pembimbing_1` (`id_dosen_pembimbing_1`),
  KEY `id_dosen_pembimbing_2` (`id_dosen_pembimbing_2`),
  KEY `id_dosen_pembimbing_3` (`id_dosen_pembimbing_3`),
  KEY `idx_pengajuan_status` (`status`),
  KEY `idx_pengajuan_tgl` (`tgl_pengajuan`),
  KEY `idx_pengajuan_mhs` (`id_riwayat_pendidikan`),
  CONSTRAINT `ta_pengajuan_judul_ibfk_1` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`),
  CONSTRAINT `ta_pengajuan_judul_ibfk_2` FOREIGN KEY (`id_riwayat_pendidikan`) REFERENCES `riwayat_pendidikan` (`id`),
  CONSTRAINT `ta_pengajuan_judul_ibfk_4` FOREIGN KEY (`id_dosen_pembimbing_1`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `ta_pengajuan_judul_ibfk_5` FOREIGN KEY (`id_dosen_pembimbing_2`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `ta_pengajuan_judul_ibfk_6` FOREIGN KEY (`id_dosen_pembimbing_3`) REFERENCES `dosen_data` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.ta_seminar_proposal
CREATE TABLE IF NOT EXISTS `ta_seminar_proposal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_tahun_akademik` bigint unsigned NOT NULL,
  `id_riwayat_pendidikan` bigint unsigned NOT NULL,
  `judul` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abstrak` text COLLATE utf8mb4_unicode_ci,
  `tgl_pengajuan` date NOT NULL,
  `tgl_ujian` date DEFAULT NULL,
  `ruangan_ujian` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_acc_judul` date DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_kwitansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_surat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_plagiasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_dosen_pembimbing_1` bigint unsigned DEFAULT NULL,
  `id_dosen_pembimbing_2` bigint unsigned DEFAULT NULL,
  `id_dosen_pembimbing_3` bigint unsigned DEFAULT NULL,
  `status_dosen_1` enum('pending','lulus','tidak_lulus','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status_dosen_2` enum('pending','lulus','tidak_lulus','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status_dosen_3` enum('pending','lulus','tidak_lulus','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `nilai_dosen_1` decimal(5,2) DEFAULT NULL,
  `nilai_dosen_2` decimal(5,2) DEFAULT NULL,
  `nilai_dosen_3` decimal(5,2) DEFAULT NULL,
  `file_revisi_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_revisi_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_revisi_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ctt_revisi_dosen_1` text COLLATE utf8mb4_unicode_ci,
  `ctt_revisi_dosen_2` text COLLATE utf8mb4_unicode_ci,
  `ctt_revisi_dosen_3` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','disetujui','ditolak','revisi','selesai') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_tahun_akademik` (`id_tahun_akademik`),
  KEY `id_dosen_pembimbing_1` (`id_dosen_pembimbing_1`),
  KEY `id_dosen_pembimbing_2` (`id_dosen_pembimbing_2`),
  KEY `id_dosen_pembimbing_3` (`id_dosen_pembimbing_3`),
  KEY `idx_proposal_status` (`status`),
  KEY `idx_proposal_tgl` (`tgl_pengajuan`),
  KEY `idx_proposal_mhs` (`id_riwayat_pendidikan`),
  KEY `idx_proposal_ujian` (`tgl_ujian`),
  CONSTRAINT `ta_seminar_proposal_ibfk_1` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`),
  CONSTRAINT `ta_seminar_proposal_ibfk_2` FOREIGN KEY (`id_riwayat_pendidikan`) REFERENCES `riwayat_pendidikan` (`id`),
  CONSTRAINT `ta_seminar_proposal_ibfk_3` FOREIGN KEY (`id_dosen_pembimbing_1`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `ta_seminar_proposal_ibfk_4` FOREIGN KEY (`id_dosen_pembimbing_2`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `ta_seminar_proposal_ibfk_5` FOREIGN KEY (`id_dosen_pembimbing_3`) REFERENCES `dosen_data` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.ta_skripsi
CREATE TABLE IF NOT EXISTS `ta_skripsi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_tahun_akademik` bigint unsigned NOT NULL,
  `id_riwayat_pendidikan` bigint unsigned NOT NULL,
  `judul` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abstrak` text COLLATE utf8mb4_unicode_ci,
  `tgl_pengajuan` date NOT NULL,
  `tgl_ujian` date DEFAULT NULL,
  `ruangan_ujian` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_acc_skripsi` date DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ppt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_plagiasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_kwitansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_surat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ktm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_khs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_kartu_bimbingan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_sertifikat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_quisioner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_dosen_pembimbing_1` bigint unsigned DEFAULT NULL,
  `id_dosen_pembimbing_2` bigint unsigned DEFAULT NULL,
  `id_dosen_pembimbing_3` bigint unsigned DEFAULT NULL,
  `status_dosen_1` enum('pending','lulus','tidak_lulus','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status_dosen_2` enum('pending','lulus','tidak_lulus','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status_dosen_3` enum('pending','lulus','tidak_lulus','revisi') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `nilai_dosen_1` decimal(5,2) DEFAULT NULL,
  `nilai_dosen_2` decimal(5,2) DEFAULT NULL,
  `nilai_dosen_3` decimal(5,2) DEFAULT NULL,
  `file_revisi_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_revisi_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_revisi_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ctt_revisi_dosen_1` text COLLATE utf8mb4_unicode_ci,
  `ctt_revisi_dosen_2` text COLLATE utf8mb4_unicode_ci,
  `ctt_revisi_dosen_3` text COLLATE utf8mb4_unicode_ci,
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak','revisi','selesai') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_tahun_akademik` (`id_tahun_akademik`),
  KEY `id_dosen_pembimbing_1` (`id_dosen_pembimbing_1`),
  KEY `id_dosen_pembimbing_2` (`id_dosen_pembimbing_2`),
  KEY `id_dosen_pembimbing_3` (`id_dosen_pembimbing_3`),
  KEY `idx_skripsi_status` (`status`),
  KEY `idx_skripsi_tgl` (`tgl_pengajuan`),
  KEY `idx_skripsi_mhs` (`id_riwayat_pendidikan`),
  KEY `idx_skripsi_acc` (`tgl_acc_skripsi`),
  CONSTRAINT `ta_skripsi_ibfk_1` FOREIGN KEY (`id_tahun_akademik`) REFERENCES `tahun_akademik` (`id`),
  CONSTRAINT `ta_skripsi_ibfk_2` FOREIGN KEY (`id_riwayat_pendidikan`) REFERENCES `riwayat_pendidikan` (`id`),
  CONSTRAINT `ta_skripsi_ibfk_3` FOREIGN KEY (`id_dosen_pembimbing_1`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `ta_skripsi_ibfk_4` FOREIGN KEY (`id_dosen_pembimbing_2`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `ta_skripsi_ibfk_5` FOREIGN KEY (`id_dosen_pembimbing_3`) REFERENCES `dosen_data` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.ulasans
CREATE TABLE IF NOT EXISTS `ulasans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `objek` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bintang` int NOT NULL DEFAULT '5',
  `komentar` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ulasans_user_id_foreign` (`user_id`),
  CONSTRAINT `ulasans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table siakad.wisuda_mahasiswas
CREATE TABLE IF NOT EXISTS `wisuda_mahasiswas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_riwayat_pendidikan` bigint unsigned NOT NULL,
  `bebas_prodi` tinyint(1) NOT NULL DEFAULT '0',
  `bebas_fakultas` tinyint(1) NOT NULL DEFAULT '0',
  `bebas_perpustakaan` tinyint(1) NOT NULL DEFAULT '0',
  `bebas_keuangan` tinyint(1) NOT NULL DEFAULT '0',
  `nama_arab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir_arab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_malang` text COLLATE utf8mb4_unicode_ci,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pas_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_pembimbing_1` bigint unsigned DEFAULT NULL,
  `id_pembimbing_2` bigint unsigned DEFAULT NULL,
  `id_periode_wisuda` bigint unsigned DEFAULT NULL,
  `status_pendaftaran` enum('Proses','Disetujui','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Proses',
  `tanggal_daftar` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wisuda_mahasiswas_id_riwayat_pendidikan_foreign` (`id_riwayat_pendidikan`),
  KEY `wisuda_mahasiswas_id_pembimbing_1_foreign` (`id_pembimbing_1`),
  KEY `wisuda_mahasiswas_id_pembimbing_2_foreign` (`id_pembimbing_2`),
  KEY `wisuda_mahasiswas_id_periode_wisuda_foreign` (`id_periode_wisuda`),
  CONSTRAINT `wisuda_mahasiswas_id_pembimbing_1_foreign` FOREIGN KEY (`id_pembimbing_1`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `wisuda_mahasiswas_id_pembimbing_2_foreign` FOREIGN KEY (`id_pembimbing_2`) REFERENCES `dosen_data` (`id`),
  CONSTRAINT `wisuda_mahasiswas_id_periode_wisuda_foreign` FOREIGN KEY (`id_periode_wisuda`) REFERENCES `periode_wisudas` (`id`),
  CONSTRAINT `wisuda_mahasiswas_id_riwayat_pendidikan_foreign` FOREIGN KEY (`id_riwayat_pendidikan`) REFERENCES `riwayat_pendidikan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengeluaran data tidak dipilih.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
