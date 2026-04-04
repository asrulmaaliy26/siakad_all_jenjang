# Panduan Navigasi Peminjaman Sarpras (Sarana dan Prasarana)

Dokumen ini menjelaskan alur kerja dan navigasi dalam sistem peminjaman barang di aplikasi SIAKAD, baik untuk Mahasiswa/Pengajar maupun Admin.

---

## 1. Alur untuk Mahasiswa & Pengajar (Peminjam)

### A. Melakukan Pengajuan Pinjaman
1. Masuk ke Menu **Sarpras / Inventaris > Peminjaman**.
2. Klik tombol **Buat Peminjaman** (atau *Create*).
3. Isi data barang yang ingin dipinjam, jumlah, serta estimasi tanggal peminjaman dan pengembalian.
4. Klik **Simpan**. Status peminjaman akan menjadi `Diajukan` (Menunggu persetujuan Admin).

### B. Mencetak Surat Peminjaman
1. Tunggu hingga status berubah menjadi `Disetujui` oleh Admin.
2. Cari record peminjaman Anda di daftar.
3. Klik tombol **Cetak Surat** (Ikon Printer biru).
4. Surat akan muncul dalam format PDF untuk dicetak sebagai bukti pengambilan barang di gudang/petugas.

### C. Mengembalikan Barang
1. Setelah barang selesai digunakan, kembali ke menu **Peminjaman**.
2. Klik tombol **Kembalikan** (Ikon Backspace oranye) pada baris barang terkait.
3. Status akan berubah menjadi `Dikembalikan` dan sejarah peminjaman Anda akan tersimpan secara otomatis.

---

## 2. Alur untuk Admin (Petugas Sarpras)

### A. Menyetujui Pengajuan
1. Masuk ke menu **Peminjaman**.
2. Cari pengajuan dengan status `Diajukan` (Background kuning).
3. Klik tombol **Setujui** (Ikon Check hijau).
   - **Otomatis**: Sistem akan men-generate nomor surat dan membuat data di menu **Surat Keluar**.
4. Jika tidak layak, klik tombol **Tolak** (Ikon Silang merah).

### B. Penyerahan Barang
1. Saat Mahasiswa/Dosen datang membawa surat cetak, Admin mencocokkan data.
2. Klik tombol **Ambil Barang** (Ikon Keranjang biru) untuk menandai bahwa barang sudah benar-benar berpindah tangan.
3. Status berubah menjadi `Dipinjam`.

### C. Monitoring Riwayat
1. Semua riwayat tersimpan dan dapat difilter berdasarkan status atau nama peminjam.
2. Admin dapat melihat lampiran surat keluar yang terkait secara langsung.

---

## Ringkasan Status
- **Diajukan**: Menunggu Approval.
- **Disetujui**: Surat siap cetak (Barang belum diambil).
- **Dipinjam**: Barang sedang dibawa oleh peminjam.
- **Dikembalikan**: Transaksi selesai, barang telah kembali.
- **Ditolak**: Pengajuan tidak disetujui.
- **Telat**: Melewati batas estimasi pengembalian.
