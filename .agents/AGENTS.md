# Aturan dan Pengetahuan Spesifik Proyek (SIAKAD)

## 1. Domain Mahasiswa & Struktur Data
Secara arsitektur, data mahasiswa di sistem SIAKAD ini tidak disimpan dalam satu tabel monolitik, melainkan terdistribusi sesuai dengan life-cycle:
- **`siswa_data_pendaftar`**: Data pendaftar / calon mahasiswa (nomor pendaftaran, jalur PMB, pilihan prodi, nilai seleksi, dokumen).
- **`siswa_data`**: Profil utama/inti mahasiswa (Biodata lengkap, identitas kependudukan, asal sekolah). Memiliki `user_id` untuk otentikasi.
- **`siswa_data_orang_tua`**: Data wali/orang tua yang terelasi 1:1 dengan `siswa_data`.
- **`riwayat_pendidikan`**: Track record akademik. Tabel ini mendefinisikan seorang mahasiswa terdaftar di jurusan mana, angkatan berapa, dan status aktifnya. (Satu mahasiswa bisa memiliki lebih dari satu riwayat jika mutasi/pindah prodi).
- **`akademik_krs`**: Kartu Rencana Studi. Dibuat per semester per mahasiswa (merujuk ke `riwayat_pendidikan`).
- **`siswa_data_ljk`**: Lembar Jawaban Komputer (atau nilai per mata kuliah). Relasi ke `akademik_krs`. Di sinilah Nilai Akhir, UTS, UAS, dan Tugas disimpan.
- **Tugas Akhir**: `ta_pengajuan_judul`, `ta_seminar_proposal`, `ta_skripsi`. Terhubung ke `riwayat_pendidikan`.

## 2. Penentuan & Perubahan Angkatan (Batch)
Angkatan seorang mahasiswa ditentukan secara dinamis (bukan satu field statis di tabel mahasiswa). Berikut adalah urutan prioritas cara sistem menentukan angkatan (seperti yang diimplementasikan di `SiswaDataTable.php`):
1. **Prioritas 1 (Status Aktif)**: Dicek melalui relasi `riwayatPendidikanAktif` (tabel `riwayat_pendidikan`). Angkatan diambil dari relasi tabel `tahun_akademik` (contoh: "2024/2025" -> Angkatan 2024).
2. **Prioritas 2 (Status Tidak Aktif)**: Jika tidak ada yang aktif, dicek melalui `riwayatPendidikanTerbaru`.
3. **Prioritas 3 (Belum Punya Riwayat)**: Jika belum menjadi mahasiswa resmi (hanya mendaftar), maka dicek dari tahun pada `Tgl_Daftar` di tabel `siswa_data_pendaftar`.

**Cara Mengubah Angkatan Mahasiswa:**
Jika ingin mengubah angkatan mahasiswa secara backend/database, JANGAN mencari kolom `angkatan` di tabel `siswa_data`. Perubahan harus dilakukan di tabel `riwayat_pendidikan`:
1. Ubah `id_tahun_akademik` pada tabel `riwayat_pendidikan` milik mahasiswa tersebut menjadi ID Tahun Akademik yang merepresentasikan angkatan yang diinginkan.
2. Jika mahasiswa tersebut masih berstatus calon/pendaftar (belum punya riwayat akademik), angkatannya diubah dengan merevisi parameter tahun pada kolom `Tgl_Daftar` di tabel `siswa_data_pendaftar`.

## 3. Aturan Operasional Tabel & Sistem
- **Menghapus Mahasiswa**: Jika menghapus record di `siswa_data` secara manual dari DBMS, akan ada konstrain. Melalui aplikasi, model events (di `SiswaData.php`) mencegah penghapusan jika mahasiswa sudah punya `riwayat_pendidikan`. Menghapus `siswa_data` juga otomatis memicu penghapusan relasinya ke tabel pendaftar, orang tua, dan `users`.
- **Semester Mahasiswa**: Perhitungan semester berjalan dihitung secara dinamis berdasarkan fungsi `getSemester()` di `RiwayatPendidikan.php`. Fungsi ini menghitung selisih antara `tanggal_mulai` dengan tanggal saat ini, menggunakan pendekatan siklus periode genap/ganjil. Tidak ada field hardcode `semester_saat_ini` di profil mahasiswa.
- **Pengisian Nilai**: Setiap perubahan nilai di tabel `siswa_data_ljk` (Tugas 1-12, UTS, UAS, Performance) akan secara otomatis (*booted event*) mengkalkulasi ulang `Nilai_Akhir`, mengkonversi ke `Nilai_Huruf`, dan menentukan `Status_Nilai` Lulus atau Tidak Lulus.
- **Validasi Koma**: Input desimal yang menggunakan koma pada LJK akan dikonversi ke titik (.) secara otomatis sebelum dieksekusi oleh database.

## 4. Flow Paten Kalkulasi Akademik (Nilai & IPS)
- **Skala Pengisian Nilai LJK**: Nilai UTS, UAS, Performance, dan Tugas yang diisi oleh dosen di tabel `siswa_data_ljk` harus menggunakan **skala 0-100**. Sistem otomatis mencari rata-rata nilai dari komponen yang bernilai > 0, lalu melakukan pembulatan (`Nilai_Akhir`).
- **Konversi Huruf Mutu & Bobot**: Nilai rata-rata 100-skala otomatis dikonversi ke `Nilai_Huruf` (A, A-, B+, B, dll.) lewat metode `calculateGradeLetter` di `SiswaDataLJK`. Huruf ini kemudian bisa ditarik nilai bobot aslinya (0-4.00) melalui accessor `$ljk->bobot` (memanggil `getBobotDariHuruf()`).
- **Perhitungan Indeks Prestasi Semester (IPS)**: Saat KRS baru dibuat, sistem mendeaktivasi KRS lama dan menghitung IPS. Rumus paten IPS yang digunakan adalah perkalian antara Bobot Nilai (0-4.00) dengan SKS mata kuliah: $\frac{\sum (Bobot\_Nilai \times SKS\_Matkul)}{\sum SKS\_Matkul}$. SKS mata kuliah didapat dengan melakukan _lazy loading_ relasi ke `mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.bobot`.

## 5. Pembaruan Filament v4 (Unified Actions)
Mulai dari pembaruan Filament v4, namespace untuk berbagai Action telah disatukan.
- JANGAN gunakan namespace spesifik seperti \Filament\Tables\Actions\ImportAction.
- SELALU gunakan namespace global yaitu \Filament\Actions\ImportAction (berlaku juga untuk action lainnya).

## 6. Import/Export Menggunakan Excel
Seluruh fitur Import dan Export dalam aplikasi ini HARUS selalu menggunakan format Excel (.xlsx).
JANGAN menggunakan Filament native ImportAction atau ExportAction jika hanya mendukung CSV.
Untuk Import, gunakan custom Action dengan FileUpload (acceptedFileTypes: aplikasi excel) dan panggil Maatwebsite\Excel\Facades\Excel::import().
Untuk Export, gunakan Maatwebsite\Excel\Facades\Excel::download() (bisa dibantu pxlrbt/filament-excel jika memungkinkan format xlsx).
For Export, gunakan Maatwebsite\Excel\Facades\Excel::download() (bisa dibantu pxlrbt/filament-excel jika memungkinkan format xlsx).

## 7. Pagination Tabel (All Records)
Semua tabel di dalam aplikasi Filament **HARUS selalu menyertakan opsi 'all'** pada pilihan pagination (jumlah baris per halaman) agar admin/pengguna bisa menampilkan semua data dalam satu halaman utuh jika dibutuhkan. 
Pengaturan ini di-set secara global di `AppServiceProvider` melalui metode `Table::configureUsing()`, sehingga setiap pembuatan tabel custom **tidak boleh menimpa** opsi ini (jangan menggunakan `paginated([10, 25])` tanpa memasukkan opsi `'all'`).

## 8. Pencatatan Perubahan Database (Migration/SQL)
Jika ada perubahan struktur database (seperti menambah kolom, mengubah tipe data, menambah foreign key, dsb.) baik melalui migration maupun query SQL langsung, perubahan tersebut HARUS dicatat beserta query/struktur terbarunya di dalam dokumen [DATABASE_CHANGES.md](DATABASE_CHANGES.md) agar menjadi acuan dengan prioritas tertinggi untuk pengembangan selanjutnya.
