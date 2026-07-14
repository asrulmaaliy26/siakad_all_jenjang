# Riwayat Perubahan Database (SQL)

Dokumen ini digunakan untuk mencatat setiap perubahan struktur database (seperti menambah tabel, menambah kolom, mengubah tipe data, menambah foreign key, dsb.) yang dilakukan langsung menggunakan _query_ SQL (terutama yang menggunakan Tinker/DB::statement atau eksekusi _raw_ SQL lainnya).

Ini berfungsi sebagai sumber kebenaran (_source of truth_) untuk pengembangan sistem dan mempermudah proses sinkronisasi apabila database direplikasi ke server lain.

---

## 14 Juli 2026 - Perubahan Kolom PDDIKTI

Penambahan kolom terkait integrasi format import nilai PDDIKTI pada tabel data master `jurusan`, `tahun_akademik`, dan `kelas`.

```sql
ALTER TABLE jurusan ADD kode_prodi VARCHAR(255) NULL AFTER nama;
ALTER TABLE tahun_akademik ADD kode_pddikti VARCHAR(255) NULL AFTER status;
ALTER TABLE kelas ADD kode_pddikti VARCHAR(255) NULL AFTER status_aktif;
```

## Seeding Kode PDDIKTI Jurusan (Prodi)

```sql
UPDATE jurusan SET kode_prodi = '86237' WHERE nama LIKE '%MANAJEMEN PENDIDIKAN ISLAM%';
UPDATE jurusan SET kode_prodi = '70241' WHERE nama LIKE '%STUDI ISLAM%';
UPDATE jurusan SET kode_prodi = '76231' WHERE nama LIKE '%ILMU AL-QUR''AN DAN TAFSIR%';
```

## Seeding Kode PDDIKTI Kelas (Auto-Generate)

```sql
UPDATE kelas SET kode_pddikti = 'MPI2A' WHERE id = 1;
UPDATE kelas SET kode_pddikti = 'MPI2B' WHERE id = 2;
UPDATE kelas SET kode_pddikti = 'IAT2A' WHERE id = 3;
UPDATE kelas SET kode_pddikti = 'SI2A' WHERE id = 4;
UPDATE kelas SET kode_pddikti = 'MPI1C' WHERE id = 5;
UPDATE kelas SET kode_pddikti = 'MPI1A' WHERE id = 7;
UPDATE kelas SET kode_pddikti = 'MPI1B' WHERE id = 8;
UPDATE kelas SET kode_pddikti = 'IAT1A' WHERE id = 9;
UPDATE kelas SET kode_pddikti = 'SI1A' WHERE id = 10;
UPDATE kelas SET kode_pddikti = 'MPI2C' WHERE id = 11;
```

## Seeding Kode PDDIKTI Tahun Akademik (Auto-Generate)

```sql
UPDATE tahun_akademik SET kode_pddikti = '20251' WHERE id = 1;
UPDATE tahun_akademik SET kode_pddikti = '20252' WHERE id = 2;
UPDATE tahun_akademik SET kode_pddikti = '20261' WHERE id = 3;
```

## Penambahan Kolom PDDIKTI Mahasiswa

```sql
ALTER TABLE siswa_data ADD npwp VARCHAR(50) NULL AFTER nisn;
ALTER TABLE siswa_data ADD no_hp VARCHAR(25) NULL AFTER no_telepon;
ALTER TABLE riwayat_pendidikan ADD kode_pt_asal VARCHAR(50) NULL AFTER nm_pt_asal;
ALTER TABLE riwayat_pendidikan ADD kode_prodi_asal VARCHAR(50) NULL AFTER nm_prodi_asal;
ALTER TABLE siswa_data_orang_tua ADD nik_wali VARCHAR(50) NULL AFTER Kewarganegaraan_Ibu;
ALTER TABLE siswa_data_orang_tua ADD nama_wali VARCHAR(255) NULL AFTER nik_wali;
ALTER TABLE siswa_data_orang_tua ADD tgl_lahir_wali DATE NULL AFTER nama_wali;
ALTER TABLE siswa_data_orang_tua ADD pendidikan_wali VARCHAR(50) NULL AFTER tgl_lahir_wali;
ALTER TABLE siswa_data_orang_tua ADD pekerjaan_wali VARCHAR(50) NULL AFTER pendidikan_wali;
ALTER TABLE siswa_data_orang_tua ADD penghasilan_wali VARCHAR(50) NULL AFTER pekerjaan_wali;
```

## Penambahan Tabel Master Wilayah (Kecamatan PDDIKTI)

```sql
CREATE TABLE IF NOT EXISTS ref_wilayahs (
    id_wil VARCHAR(50) PRIMARY KEY,
    kecamatan VARCHAR(255) NULL,
    kabupaten VARCHAR(255) NULL,
    provinsi VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```
