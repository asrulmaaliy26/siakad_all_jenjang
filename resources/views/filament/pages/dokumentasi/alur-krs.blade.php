<div class="prose prose-sm max-w-none leading-relaxed text-gray-800 dark:prose-invert dark:text-gray-100">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 mb-4">Alur Pengambilan Kartu Rencana Studi (KRS)</h1>
    
    <p class="mb-6">Dokumen ini menjelaskan prosedur bagaimana mahasiswa mengambil mata pelajaran (KRS), berkonsultasi dengan DPA, hingga status KRS disetujui.</p>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>📅</span> Alur Pengambilan KRS
    </h2>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahap</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas Mahasiswa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran DPA (Dosen Wali)</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status & Batasan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">1. Pembayaran</td>
                    <td class="px-4 py-3 text-sm">Mahasiswa melunasi biaya SPP/KRS untuk semester berjalan.</td>
                    <td class="px-4 py-3 text-sm">-</td>
                    <td class="px-4 py-3 text-sm">KRS hanya bisa diisi jika <strong>Status Bayar</strong> sudah "Lunas" (Y).</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">2. Pengambilan</td>
                    <td class="px-4 py-3 text-sm">Mahasiswa memilih mata pelajaran kelas melalui portal mahasiswa.</td>
                    <td class="px-4 py-3 text-sm">-</td>
                    <td class="px-4 py-3 text-sm">Total SKS tidak boleh melebihi <strong>Batas Maksimal SKS</strong> di data Akademik KRS.</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">3. Konsultasi</td>
                    <td class="px-4 py-3 text-sm">Berdiskusi dengan DPA melalui fitur Chat KRS mengenai rencana studi.</td>
                    <td class="px-4 py-3 text-sm">Memberikan arahan atau revisi mata kuliah yang diambil.</td>
                    <td class="px-4 py-3 text-sm">Mahasiswa dan DPA dapat saling mengirim pesan.</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">4. Approval</td>
                    <td class="px-4 py-3 text-sm">Menunggu persetujuan DPA.</td>
                    <td class="px-4 py-3 text-sm">Mengubah status menjadi <strong>Setuju</strong> jika rencana studi sudah sesuai.</td>
                    <td class="px-4 py-3 text-sm">Status: <strong>Syarat KRS</strong> -> "Terpenuhi" (Y).</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">5. Selesai</td>
                    <td class="px-4 py-3 text-sm">Data KRS tersimpan permanen dan muncul di daftar hadir kelas.</td>
                    <td class="px-4 py-3 text-sm">-</td>
                    <td class="px-4 py-3 text-sm">Data KRS <strong>Terkunci</strong>; tidak bisa menambah/menghapus mata kuliah lagi.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>🛠️</span> Detail Teknis & Aturan Sistem
    </h2>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">1. Batas Maksimal SKS</h3>
    <p class="mb-4">Sistem akan memvalidasi jumlah SKS yang diambil. Mahasiswa hanya dapat mengambil matakuliah hingga jumlah SKS tertentu yang ditentukan berdasarkan pencapaian akademik (IPS) semester sebelumnya:</p>
    <ul class="list-disc list-inside mb-6 space-y-1 ml-4 text-gray-700 dark:text-gray-300">
        <li><strong>IPS >= 3.00</strong>: Maksimal 24 SKS.</li>
        <li><strong>IPS >= 2.00</strong>: Maksimal 18 SKS.</li>
        <li><strong>IPS < 2.00</strong>: Maksimal 12 SKS.</li>
    </ul>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">2. Konsultasi dengan DPA (Wali Dosen)</h3>
    <p class="mb-6">Mahasiswa dapat menggunakan fitur <strong>Chat KRS</strong> untuk berdiskusi langsung dengan DPA di dalam sistem. Fitur ini memungkinkan koordinasi yang lebih cepat tanpa perlu tatap muka secara langsung untuk setiap mata kuliah yang diambil.</p>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">3. Penguncian Data (KRS Lock)</h3>
    <p class="mb-4">Setelah DPA menyatakan <strong>"Setuju"</strong> pada data Akademik KRS:</p>
    <ul class="list-disc list-inside mb-6 space-y-1 ml-4 text-gray-700 dark:text-gray-300">
        <li>Tombol "Tambah Mata Kuliah" akan menghilang.</li>
        <li>Aksi "Edit" dan "Hapus" pada daftar mata kuliah yang sudah diambil akan dinonaktifkan.</li>
        <li>Nama mahasiswa secara otomatis akan terdaftar dan muncul di seluruh <strong>Presensi/Absensi</strong> dan <strong>Jurnal Pengajaran</strong> pada kelas-kelas yang telah diambil.</li>
    </ul>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>📝</span> Referensi Status KRS
    </h2>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                <tr>
                    <td class="px-4 py-3 text-sm font-medium" rowspan="2">status_bayar</td>
                    <td class="px-4 py-3 text-sm text-center">N</td>
                    <td class="px-4 py-3 text-sm">Belum Lunas (KRS tidak bisa diakses)</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm text-center">Y</td>
                    <td class="px-4 py-3 text-sm text-center">Lunas (KRS siap diisi)</td>
                </tr>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                    <td class="px-4 py-3 text-sm font-medium" rowspan="2">syarat_krs</td>
                    <td class="px-4 py-3 text-sm text-center">N</td>
                    <td class="px-4 py-3 text-sm">Belum Disetujui (Dapat menambah/menghapus matkul)</td>
                </tr>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                    <td class="px-4 py-3 text-sm text-center">Y</td>
                    <td class="px-4 py-3 text-sm"><strong>Disetujui/Lock</strong> (KRS permanen dan tidak bisa diubah)</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium" rowspan="2">status_aktif</td>
                    <td class="px-4 py-3 text-sm text-center">Y</td>
                    <td class="px-4 py-3 text-sm">KRS Semester Berjalan</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm text-center">N</td>
                    <td class="px-4 py-3 text-sm">KRS Riwayat Lampau</td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>🖥️</span> Panduan Navigasi Mahasiswa (Langkah Demi Langkah)
    </h2>

    <div class="space-y-6">
        <section>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">1. Mengakses Menu KRS</h3>
            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                <li>Pergi ke <strong>Sidebar (Menu Samping)</strong> di sebelah kiri.</li>
                <li>Cari grup menu <strong>"Perkuliahan"</strong>.</li>
                <li>Klik menu <strong>"KRS"</strong> (Ikon: Dokumen Teks 📄).</li>
            </ul>
        </section>

        <section>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">2. Memulai Pengisian KRS</h3>
            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                <li>Di halaman daftar KRS, temukan baris KRS mahasiswa untuk semester aktif.</li>
                <li>Klik ikon <strong>Mata (Lihat)</strong> atau <strong>Pensil (Edit)</strong> di kolom aksi.</li>
                <li>Scroll ke bawah hingga menemukan section bernama <strong>"Siswa Data Ljk"</strong>.</li>
                <li>Klik tombol <strong>"+ Tambah Mata Pelajaran"</strong> (Tombol berwarna Biru).</li>
            </ul>
        </section>

        <section>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">3. Memilih Mata Kuliah (Modal)</h3>
            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                <li>Setelah klik tombol tambah, akan muncul jendela (Modal) berisi daftar mata kuliah yang tersedia.</li>
                <li>Klik tombol <strong>"+ Ambil"</strong> untuk memilih mata kuliah tersebut.</li>
                <li>Jika ingin membatalkan, klik tombol <strong>"- Batal"</strong> (Hanya selama KRS belum disetujui DPA/Locked).</li>
                <li>Mata kuliah yang sudah diambil akan muncul dalam grup <strong>"Sudah Diambil"</strong>.</li>
            </ul>
        </section>

        <section>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">4. Berkonsultasi dengan DPA</h3>
            <p class="mb-2">Ada dua cara untuk membuka fitur diskusi:</p>
            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                <li><strong>Melalui Menu Utama</strong>: Klik menu <strong>"Diskusi Pembimbing"</strong> di sidebar bawah menu KRS.</li>
                <li><strong>Melalui Header KRS</strong>: Di halaman daftar KRS, klik tombol <strong>"Diskusi Pembimbing"</strong> yang ada di bagian atas tabel (Ikon: Chat 💬).</li>
            </ul>
        </section>

        <section>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">5. Mencetak Bukti KRS/KHS</h3>
            <p class="mb-2">Jika KRS sudah disetujui (Syarat KRS: Disetujui), mahasiswa dapat mencetak bukti:</p>
            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                <li>Klik tombol <strong>"Titik Tiga (Aksi)"</strong> di samping baris KRS.</li>
                <li>Pilih <strong>"Cetak KRS"</strong> untuk mencetak Kartu Rencana Studi.</li>
                <li>Pilih <strong>"Cetak KHS"</strong> untuk mencetak Kartu Hasil Studi (jika nilai sudah keluar).</li>
            </ul>
        </section>
    </div>
</div>
