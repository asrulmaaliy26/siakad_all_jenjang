# Struktur Penempatan File Upload (SIAKAD)

Sistem SIAKAD ini menggunakan satu *helper* utama yaitu `UploadPathHelper` untuk memastikan semua file yang diunggah (upload) tertata rapi di server (`storage/app/public/uploads/...`). 

Berikut adalah dokumentasi lengkap dari semua jenis file, lokasi penempatannya, dan format penamaannya:

## 1. File Mahasiswa (Umum & Foto Profil)
Digunakan untuk data pokok mahasiswa (seperti foto profil).
- **Format Path:** `uploads/mahasiswa/mahasiswa_{tahun-angkatan}/{nama-mahasiswa}/{nama-kolom}/`
- **Contoh:** `uploads/mahasiswa/mahasiswa_2023/muhammad-lathif/foto-profil/namafile.jpg`
- **Pengaturan via:** `UploadPathHelper::uploadSiswaDataPath()`

## 2. File Pendaftaran Mahasiswa Baru (PMB)
Digunakan untuk berkas-berkas syarat pendaftaran (Ijazah, SKHU, KTP, Pas Foto, Bukti Pembayaran, Mutasi, dll).
- **Format Path:** `uploads/mahasiswa/pendaftar_{tahun-pendaftaran}/{nama-pendaftar}/{nama-syarat}/`
- **Contoh:** `uploads/mahasiswa/pendaftar_2024/budi-santoso/legalisir-ijazah/namafile.pdf`
- **Pengaturan via:** `UploadPathHelper::uploadPendaftarPath()`

## 3. File Akademik & KRS
Digunakan untuk berkas yang berkaitan dengan registrasi KRS mahasiswa per semester (Kwitansi Pembayaran, Surat Izin, dll).
- **Format Path:** `uploads/mahasiswa/mahasiswa_{tahun-akademik}/{nama-mahasiswa}/{nama-kolom}/`
- **Contoh:** `uploads/mahasiswa/mahasiswa_2023-2024-ganjil/andi-susanto/akademik-krs/namafile.pdf`
- **Pengaturan via:** `UploadPathHelper::uploadKrsPath()`

## 4. File Ujian (LJK UTS & UAS)
Digunakan untuk Lembar Jawaban Komputer (LJK) hasil ujian mahasiswa.
- **Format Path:** `uploads/mahasiswa/mahasiswa_{tahun-akademik}/{nama-mahasiswa}/{jenis-ujian}/`
- **Contoh:** `uploads/mahasiswa/mahasiswa_2023-2024-genap/siti-aminah/ljk-uts/namafile.pdf`
- **Pengaturan via:** `UploadPathHelper::uploadUjianPath()`

## 5. File Tugas Kuliah
Digunakan untuk pengumpulan tugas harian mahasiswa.
- **Format Path:** `uploads/mahasiswa/mahasiswa_{tahun-akademik}/{nama-mahasiswa}/tugas_{urutan-tugas}/`
- **Contoh:** `uploads/mahasiswa/mahasiswa_2023-2024-ganjil/budi-santoso/tugas-1/namafile.pdf`
- **Pengaturan via:** `UploadPathHelper::uploadTugasPath()`

## 6. File Tugas Akhir (Skripsi, Seminar Proposal, Pengajuan Judul)
Digunakan untuk mengunggah draft proposal, PPT, hasil turnitin/plagiasi, kuesioner, lembar revisi, dll.
- **Format Path:** `uploads/mahasiswa/mahasiswa_{tahun-akademik}/{nama-mahasiswa}/{tahapan-ta}/`
- **Contoh:** `uploads/mahasiswa/mahasiswa_2024-2025-ganjil/budi-santoso/ta-skripsi/namafile.pdf`
- **Pengaturan via:** `UploadPathHelper::uploadTaPath()`

## 7. File Dosen (Dokumen & Foto)
Digunakan untuk berkas sertifikasi, foto profil, dan dokumen pelengkap milik dosen.
- **Format Path:** `uploads/dosen/{nama-dosen}/{nama-kolom}/`
- **Contoh:** `uploads/dosen/dr-h-ahmad-zainuri/dosen-dokumen/namafile.pdf`
- **Pengaturan via:** `UploadPathHelper::uploadDosenPath()`

## 8. File Mata Pelajaran Kelas (Soal Ujian dll)
Digunakan untuk soal UTS, soal UAS, dan materi oleh dosen.
- **Format Path:** `uploads/{tahun-akademik}/{info-kelas}/{nama-mata-pelajaran}/{nama-kolom}/`
- **Contoh:** `uploads/2023-2024-ganjil/kelas-reguler-1/pendidikan-agama-islam/soal-uts/namafile.pdf`
- **Pengaturan via:** `UploadPathHelper::uploadMataPelajaranKelasPath()`

---

## 9. File Persuratan (Pengajuan Surat)
Digunakan untuk berkas pendukung pengajuan surat dan hasil file surat oleh admin.
- **Format Path:** `uploads/persuratan/{pendukung|hasil}/`
- **Contoh:** `uploads/persuratan/pendukung/namafile.pdf`
- **Pengaturan via:** *Hardcoded* (`persuratan/pendukung`, `persuratan/hasil`)

## 10. File Wisuda
Digunakan untuk mengunggah pas foto wisuda mahasiswa.
- **Format Path:** `uploads/wisuda/foto/`
- **Contoh:** `uploads/wisuda/foto/namafile.jpg`
- **Pengaturan via:** *Hardcoded* (`wisuda/foto`)

## 11. Pengaturan Pendaftaran & Web (Brosur, Banner)
Digunakan oleh Admin untuk pengaturan visual halaman pendaftaran.
- **Format Path:** `uploads/pendaftaran/{header|banner|brosur}/`
- **Contoh:** `uploads/pendaftaran/banner/namafile.jpg`
- **Pengaturan via:** *Hardcoded* (`pendaftaran/header`, dll)

## 12. File Perpustakaan (Library)
Digunakan untuk mengunggah cover buku.
- **Format Path:** `uploads/library/covers/`
- **Contoh:** `uploads/library/covers/namafile.jpg`
- **Pengaturan via:** *Hardcoded* (`library/covers`)

## 13. File Import Excel (Sistem)
Digunakan untuk menampung sementara file impor `.xlsx`.
- **Format Path:** `uploads/imports/{jenis-import}/`
- **Contoh:** `uploads/imports/mata-pelajaran-kelas/namafile.xlsx`
- **Pengaturan via:** *Hardcoded* (`imports/...`)

---

### Aturan Penamaan File (File Naming Convention)
- Laravel (lewat Filament) secara bawaan menggunakan sistem **Hashing** acak untuk nama file PDF/Gambar (contoh: `01KV7Y8ZH4F6TVH58SZ2KJV0P6.pdf`) guna menghindari bentrok (*overwrite*) jika ada file bernama sama.
- Nama direktori atau folder (*Path*) menggunakan metode **Slug** (`Str::slug()`), yang berarti semua teks akan diubah menjadi huruf kecil (lowercase) dan spasi atau tanda baca diubah menjadi tanda strip (`-`). Contoh: `LJK UTS` menjadi `ljk-uts`.

*(Catatan: Semua file di atas bersifat publik setelah diunggah dan bisa diakses via web dengan alamat `http://[domain]/storage/uploads/...`)*
