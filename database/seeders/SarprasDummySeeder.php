<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\SarprasBarang;
use App\Models\SarprasKategori;
use App\Models\SarprasPeminjaman;
use App\Models\SarprasSuratKategori;
use App\Models\SarprasSuratKeluar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SarprasDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Sarpras Categories
        $categories = [
            ['nama_kategori' => 'Elektronik', 'deskripsi' => 'Barang-barang elektronik kampus'],
            ['nama_kategori' => 'Perabotan', 'deskripsi' => 'Mebel dan perlengkapan kantor'],
            ['nama_kategori' => 'Alat Laboratorium', 'deskripsi' => 'Peralatan praktik laboratorium'],
        ];

        foreach ($categories as $cat) {
            SarprasKategori::updateOrCreate(['nama_kategori' => $cat['nama_kategori']], $cat);
        }

        $catElektronik = SarprasKategori::where('nama_kategori', 'Elektronik')->first();
        $catPerabotan = SarprasKategori::where('nama_kategori', 'Perabotan')->first();
        $catLab = SarprasKategori::where('nama_kategori', 'Alat Laboratorium')->first();

        // 2. Get some Jurusans and Users
        $jurusans = Jurusan::all();
        $users = User::all();

        if ($jurusans->isEmpty() || $users->isEmpty()) {
            $this->command->error('Users or Jurusans table is empty. Please seed them first.');
            return;
        }

        // 3. Create Sample Items (Barang)
        $items = [
            [
                'kode_barang' => 'ELC-001',
                'nama_barang' => 'Laptop ASUS ROG',
                'merek' => 'ASUS',
                'sarpras_kategori_id' => $catElektronik->id,
                'jumlah' => 10,
                'kondisi' => 'Baik',
                'status_penggunaan' => 'Tersedia',
                'tanggal_pengadaan' => '2025-01-15',
                'id_jurusan' => $jurusans->random()->id,
            ],
            [
                'kode_barang' => 'ELC-002',
                'nama_barang' => 'Sound System TOA',
                'merek' => 'TOA',
                'sarpras_kategori_id' => $catElektronik->id,
                'jumlah' => 5,
                'kondisi' => 'Baik',
                'status_penggunaan' => 'Tersedia',
                'tanggal_pengadaan' => '2024-11-20',
                'id_jurusan' => $jurusans->random()->id,
            ],
            [
                'kode_barang' => 'FUR-001',
                'nama_barang' => 'Meja Dosen Kayu Jati',
                'merek' => 'Lokal',
                'sarpras_kategori_id' => $catPerabotan->id,
                'jumlah' => 20,
                'kondisi' => 'Baik',
                'status_penggunaan' => 'Digunakan',
                'tanggal_pengadaan' => '2023-05-10',
                'id_jurusan' => $jurusans->random()->id,
            ],
            [
                'kode_barang' => 'FUR-002',
                'nama_barang' => 'Kursi Mahasiswa Chitose',
                'merek' => 'Chitose',
                'sarpras_kategori_id' => $catPerabotan->id,
                'jumlah' => 100,
                'kondisi' => 'Rusak Ringan',
                'status_penggunaan' => 'Digunakan',
                'tanggal_pengadaan' => '2023-05-10',
                'id_jurusan' => $jurusans->random()->id,
            ],
            [
                'kode_barang' => 'LAB-001',
                'nama_barang' => 'Mikroskop Binokuler',
                'merek' => 'Olympus',
                'sarpras_kategori_id' => $catLab->id,
                'jumlah' => 15,
                'kondisi' => 'Baik',
                'status_penggunaan' => 'Tersedia',
                'tanggal_pengadaan' => '2025-02-01',
                'id_jurusan' => $jurusans->random()->id,
            ],
        ];

        foreach ($items as $item) {
            SarprasBarang::updateOrCreate(['kode_barang' => $item['kode_barang']], $item);
        }

        // 4. Create Letter Categories
        $letterCats = [
            [
                'nama' => 'Surat Peminjaman Barang',
                'kode' => 'PINJAM',
                'format_nomor' => '{counter}/SIAKAD/SARPRAS/{kode}/{year}/{month}',
            ],
            [
                'nama' => 'Surat Pengembalian Barang',
                'kode' => 'KEMBALI',
                'format_nomor' => '{counter}/SIAKAD/SARPRAS/{kode}/{year}/{month}',
            ],
        ];

        foreach ($letterCats as $lcat) {
            SarprasSuratKategori::updateOrCreate(['kode' => $lcat['kode']], $lcat);
        }

        $pinjamCat = SarprasSuratKategori::where('kode', 'PINJAM')->first();

        // 5. Create some Loans (Peminjaman)
        $barangPinjam = SarprasBarang::where('status_penggunaan', 'Tersedia')->first();
        if ($barangPinjam) {
            $user = $users->random();
            $peminjaman = SarprasPeminjaman::create([
                'sarpras_barang_id' => $barangPinjam->id,
                'user_id' => $user->id,
                'jumlah_pinjam' => 1,
                'tanggal_pinjam' => now()->subDays(2),
                'estimasi_kembali' => now()->addDays(5),
                'status' => 'Dipinjam',
                'keterangan' => 'Pinjam untuk kegiatan UKM',
            ]);

            // Create accompanying letter
            $surat = SarprasSuratKeluar::create([
                'sarpras_surat_kategori_id' => $pinjamCat->id,
                'user_id' => $user->id,
                'nomor_surat' => SarprasSuratKeluar::generateNomorSurat($pinjamCat->id),
                'perihal' => 'Peminjaman Barang: ' . $barangPinjam->nama_barang,
                'tujuan' => $user->name,
                'tanggal_surat' => now()->subDays(2),
                'isi_surat' => "<p>Dengan ini menerangkan bahwa:</p><ul><li><strong>Nama Barang:</strong> {$barangPinjam->nama_barang}</li><li><strong>Jumlah:</strong> 1 unit</li><li><strong>Tanggal Pinjam:</strong> " . now()->subDays(2)->format('d-m-Y') . "</li><li><strong>Estimasi Kembali:</strong> " . now()->addDays(5)->format('d-m-Y') . "</li></ul>",
                'status' => 'Sent',
            ]);

            $peminjaman->update(['sarpras_surat_keluar_id' => $surat->id]);
        }
    }
}
