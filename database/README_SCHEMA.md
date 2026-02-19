# 📚 SIAKAD Database Schema Documentation

## 🎯 Konvensi Penamaan Field

### ✅ Foreign Key ke Tabel Biasa

Gunakan prefix `id_` untuk foreign key yang menunjuk ke tabel biasa:

```
id_jurusan          -> jurusan.id
id_fakultas         -> fakultas.id
id_siswa_data       -> siswa_data.id
id_jenjang_pendidikan -> jenjang_pendidikan.id
```

### ✅ Foreign Key ke Reference Option

Gunakan prefix `ro_` untuk foreign key yang menunjuk ke tabel `reference_option`:

```
ro_program_kelas    -> reference_option.id (nama_grup='program_kelas')
ro_ruang_kelas      -> reference_option.id (nama_grup='ruang_kelas')
ro_status_siswa     -> reference_option.id (nama_grup='status_siswa')
ro_agama            -> reference_option.id (nama_grup='agama')
ro_pangkat_gol      -> reference_option.id (nama_grup='pangkat')
ro_jabatan          -> reference_option.id (nama_grup='jabatan_fungsional')
ro_status_dosen     -> reference_option.id (nama_grup='status_dosen')
ro_jns_daftar       -> reference_option.id (nama_grup='jns_pendaftaran')
ro_jns_keluar       -> reference_option.id (nama_grup='jns_keluar')
ro_program_sekolah  -> reference_option.id (nama_grup='program_sekolah')
```

## 📊 Struktur Tabel

### 1️⃣ **reference_option** (PALING PENTING - HARUS PERTAMA!)

Tabel master untuk semua pilihan/option yang digunakan di sistem.

**Kolom:**

- `id` - Primary key
- `nama_grup` - Nama grup option (program_kelas, ruang_kelas, dll)
- `kode` - Kode singkat (A, B, R101, dll)
- `nilai` - Label yang ditampilkan (Reguler Pagi, Ruang 101, dll)
- `status` - Y/N (aktif/tidak)
- `deskripsi` - Deskripsi tambahan (optional)

**Grup yang tersedia:**

- `program_kelas` - Program kelas (Reguler Pagi, Reguler Sore, Karyawan)
- `ruang_kelas` - Ruang kelas (R101, R102, Lab Komputer, dll)
- `status_siswa` - Status mahasiswa (Aktif, Cuti, Lulus, Keluar, DO)
- `agama` - Agama (Islam, Kristen, Katolik, dll)
- `status_dosen` - Status dosen (Tetap, Tidak Tetap)
- `pangkat` - Pangkat golongan dosen
- `jabatan_fungsional` - Jabatan fungsional dosen
- `jns_pendaftaran` - Jenis pendaftaran mahasiswa
- `jns_keluar` - Jenis keluar mahasiswa
- `program_sekolah` - Program sekolah

### 2️⃣ **jenjang_pendidikan**

Jenjang pendidikan (S1, S2, S3, D3)

### 3️⃣ **tahun_akademik**

Tahun akademik dan periode (2024/2025 Ganjil, dll)

### 4️⃣ **fakultas**

Fakultas di institusi

### 5️⃣ **jurusan**

Program studi di bawah fakultas

- FK: `id_fakultas` → fakultas

### 6️⃣ **dosen_data**

Data dosen

- FK: `id_jurusan` → jurusan
- FK: `ro_pangkat_gol` → reference_option
- FK: `ro_jabatan` → reference_option
- FK: `ro_status_dosen` → reference_option
- FK: `ro_agama` → reference_option

### 7️⃣ **mata_pelajaran_master**

Master mata kuliah

- FK: `id_jurusan` → jurusan

### 8️⃣ **kurikulum**

Kurikulum per jurusan dan tahun akademik

- FK: `id_jurusan` → jurusan
- FK: `id_tahun_akademik` → tahun_akademik
- FK: `id_jenjang_pendidikan` → jenjang_pendidikan

### 9️⃣ **mata_pelajaran_kurikulum**

Mata kuliah yang ada di kurikulum

- FK: `id_kurikulum` → kurikulum
- FK: `id_mata_pelajaran_master` → mata_pelajaran_master

### 🔟 **kelas**

Kelas perkuliahan

- FK: `ro_program_kelas` → reference_option
- FK: `id_jenjang_pendidikan` → jenjang_pendidikan
- FK: `id_tahun_akademik` → tahun_akademik
- FK: `id_jurusan` → jurusan (optional)

### 1️⃣1️⃣ **mata_pelajaran_kelas**

Jadwal mata kuliah di kelas (relasi many-to-many antara mata_pelajaran_kurikulum dan kelas)

- FK: `id_mata_pelajaran_kurikulum` → mata_pelajaran_kurikulum
- FK: `id_kelas` → kelas
- FK: `id_dosen_data` → dosen_data
- FK: `ro_ruang_kelas` → reference_option

### 1️⃣2️⃣ **siswa_data**

Data mahasiswa/siswa

### 1️⃣3️⃣ **riwayat_pendidikan**

Riwayat pendidikan mahasiswa (satu siswa bisa punya banyak riwayat)

- FK: `id_siswa_data` → siswa_data
- FK: `id_jenjang_pendidikan` → jenjang_pendidikan
- FK: `id_jurusan` → jurusan
- FK: `ro_program_sekolah` → reference_option
- FK: `ro_status_siswa` → reference_option
- FK: `ro_jns_daftar` → reference_option
- FK: `ro_jns_keluar` → reference_option

### 1️⃣4️⃣ **akademik_krs**

KRS mahasiswa (Kartu Rencana Studi)

- FK: `id_riwayat_pendidikan` → riwayat_pendidikan
- FK: `id_kelas` → kelas

### 1️⃣5️⃣ **pertemuan_kelas**

Pertemuan/sesi kelas

- FK: `id_mata_pelajaran_kelas` → mata_pelajaran_kelas

### 1️⃣6️⃣ **absensi_siswa**

Absensi mahasiswa

- FK: `id_krs` → akademik_krs
- FK: `id_mata_pelajaran_kelas` → mata_pelajaran_kelas

### 1️⃣7️⃣ **siswa_data_ljk**

Nilai mahasiswa (Lembar Jawaban Komputer)

- FK: `id_akademik_krs` → akademik_krs
- FK: `id_mata_pelajaran_kelas` → mata_pelajaran_kelas

## 🔄 Relasi Antar Tabel

### Hirarki Akademik:

```
fakultas
  └── jurusan
        ├── mata_pelajaran_master
        └── kurikulum
              └── mata_pelajaran_kurikulum
                    └── mata_pelajaran_kelas
```

### Hirarki Mahasiswa:

```
siswa_data
  └── riwayat_pendidikan
        └── akademik_krs
              ├── absensi_siswa
              └── siswa_data_ljk
```

### Relasi Kelas:

```
kelas
  ├── ro_program_kelas (reference_option)
  ├── id_jenjang_pendidikan
  ├── id_tahun_akademik
  └── mata_pelajaran_kelas
        ├── id_mata_pelajaran_kurikulum
        ├── id_dosen_data
        ├── ro_ruang_kelas (reference_option)
        ├── pertemuan_kelas
        ├── absensi_siswa
        └── siswa_data_ljk
```

## 📝 Cara Menggunakan

### 1. Import Schema

```bash
mysql -u root -p siakad < database/siakad_schema.sql
```

### 2. Atau via Laravel Migration

```bash
php artisan migrate:fresh --seed
```

### 3. Query Reference Option

```sql
-- Ambil semua program kelas
SELECT * FROM reference_option WHERE nama_grup = 'program_kelas';

-- Ambil ruang kelas aktif
SELECT * FROM reference_option
WHERE nama_grup = 'ruang_kelas' AND status = 'Y';

-- Ambil status siswa
SELECT * FROM reference_option WHERE nama_grup = 'status_siswa';
```

### 4. Join dengan Reference Option

```sql
-- Ambil data kelas dengan nama program
SELECT k.*, ro.nilai as nama_program
FROM kelas k
LEFT JOIN reference_option ro ON k.ro_program_kelas = ro.id
WHERE k.status_aktif = 'Y';

-- Ambil data dosen dengan agama
SELECT d.nama, ro.nilai as agama
FROM dosen_data d
LEFT JOIN reference_option ro ON d.ro_agama = ro.id;
```

## ⚠️ Catatan Penting

1. **Urutan Insert Data:**
    - reference_option (PERTAMA!)
    - jenjang_pendidikan
    - tahun_akademik
    - fakultas
    - jurusan
    - dosen_data
    - mata_pelajaran_master
    - kurikulum
    - mata_pelajaran_kurikulum
    - kelas
    - mata_pelajaran_kelas
    - siswa_data
    - riwayat_pendidikan
    - akademik_krs
    - pertemuan_kelas
    - absensi_siswa
    - siswa_data_ljk

2. **Foreign Key Constraints:**
    - Semua FK menggunakan `ON DELETE CASCADE` atau `ON DELETE SET NULL`
    - Pastikan data parent ada sebelum insert data child

3. **Enum Values:**
    - `status`: 'Y' atau 'N'
    - `jenis_kelamin`: 'L' atau 'P'
    - `status_bayar`: 'Y' atau 'N'
    - `status_aktif`: 'Y' atau 'N'

4. **Reference Option:**
    - Selalu gunakan `nama_grup` untuk filter
    - `kode` untuk identifier singkat
    - `nilai` untuk display name
    - `status` untuk aktif/nonaktif

## 🔧 Troubleshooting

### Error: Cannot add foreign key constraint

**Solusi:** Pastikan data di reference*option sudah ada sebelum insert data yang menggunakan ro*\*

### Error: Duplicate entry

**Solusi:** Gunakan `INSERT IGNORE` atau check existing data terlebih dahulu

### Error: Data too long

**Solusi:** Sesuaikan panjang varchar atau gunakan TEXT untuk data panjang

## 📞 Support

Jika ada pertanyaan atau masalah, silakan hubungi tim development.
