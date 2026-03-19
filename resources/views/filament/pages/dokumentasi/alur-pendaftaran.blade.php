<div class="prose prose-sm max-w-none leading-relaxed text-gray-800 dark:prose-invert dark:text-gray-100">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 mb-4">Alur Pendaftaran & Seleksi Mahasiswa Baru</h1>
    
    <p class="mb-6">Dokumen ini menjelaskan alur kerja sistem SIAKAD mulai dari pendaftaran mahasiswa hingga status "Diterima" dan menjadi Mahasiswa Aktif.</p>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>🚀</span> Alur Utama
    </h2>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahap</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pendaftaran</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Seleksi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Validasi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">1. Registrasi</td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">B</span></td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">B</span></td>
                    <td class="px-4 py-3 text-sm text-center">0</td>
                    <td class="px-4 py-3 text-sm">Pendaftar mengisi form & upload berkas awal.</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">2. Seleksi</td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">B</span></td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">B</span></td>
                    <td class="px-4 py-3 text-sm text-center">0</td>
                    <td class="px-4 py-3 text-sm">Mengikuti tahapan seleksi (Tes Tulis, Wawancara, dll).</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">3. Lulus Seleksi</td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">B</span></td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200 font-bold">Y</span></td>
                    <td class="px-4 py-3 text-sm text-center">0</td>
                    <td class="px-4 py-3 text-sm">Admin menyatakan pendaftar Lulus seleksi.</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">4. Menunggu Diterima</td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">B</span></td>
                    <td class="px-4 py-3 text-sm text-center">Y</td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-200 font-bold">1</span></td>
                    <td class="px-4 py-3 text-sm">Admin memvalidasi berkas fisik/dokumen pendaftar.</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">5. Diterima/Aktif</td>
                    <td class="px-4 py-3 text-sm text-center"><span class="px-2 py-0.5 rounded bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200 font-bold">Y</span></td>
                    <td class="px-4 py-3 text-sm text-center">Y</td>
                    <td class="px-4 py-3 text-sm text-center">1</td>
                    <td class="px-4 py-3 text-sm">Pendaftar resmi menjadi Mahasiswa Aktif.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>🔍</span> Detail Tahapan
    </h2>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">1. Tahap Seleksi</h3>
    <p class="mb-4">Pendaftar akan memiliki riwayat seleksi di tabel <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded text-primary-600 dark:text-primary-400">Program Seleksi</code>. Admin dapat menambah tahap seleksi seperti:</p>
    <ul class="list-disc list-inside mb-4 space-y-1 ml-4 text-gray-700 dark:text-gray-300">
        <li>Verifikasi Administrasi</li>
        <li>Tes Tulis</li>
        <li>Wawancara</li>
    </ul>
    <p class="mb-6 italic text-sm text-gray-600 dark:text-gray-400">Pendaftar dapat mengunggah jawaban atau bukti di tiap tahap seleksi tersebut.</p>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">2. Status Menunggu Diterima (Lulus Seleksi)</h3>
    <p class="mb-4">Setelah semua tahap seleksi selesai dan nilai mencukupi, Admin akan mengubah <strong>Status Kelulusan Seleksi</strong> menjadi <strong>"Lulus" (Y)</strong>.</p>
    <p class="mb-4">Pada tahap ini:</p>
    <ul class="list-disc list-inside mb-4 space-y-1 ml-4 text-gray-700 dark:text-gray-300">
        <li>Status pendaftaran masih "⏳ Pending/Proses".</li>
        <li>Pendaftar menunggu validasi berkas oleh Admin untuk dapat diterima secara resmi.</li>
    </ul>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">3. Validasi Dokumen</h3>
    <p class="mb-6">Admin melakukan pengecekan berkas. Jika sudah sesuai, <strong>Status Validasi</strong> diubah menjadi <strong>"Sudah Divalidasi" (1)</strong>.</p>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">4. Peresmian Mahasiswa (Status Diterima)</h3>
    <p class="mb-4">Admin mengubah <strong>Status Pendaftaran</strong> menjadi <strong>"Diterima" (Y)</strong>.</p>
    
    <div class="p-4 rounded-lg bg-warning-50 border-l-4 border-warning-400 dark:bg-warning-900/20 dark:border-warning-600 mb-6">
        <div class="flex items-center gap-2 mb-2 text-warning-800 dark:text-warning-300">
            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">IMPORTANT</span>
        </div>
        <p class="text-warning-700 dark:text-warning-400">Sistem memiliki aturan: Status Pendaftaran hanya bisa menjadi "Diterima" jika <strong>Status Validasi</strong> sudah "Sudah Divalidasi" DAN <strong>Status Seleksi</strong> sudah "Lulus".</p>
    </div>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>⚙️</span> Proses Otomatis Setelah "Diterima"
    </h2>

    <p class="mb-4">Ketika pendaftar diubah statusnya menjadi <strong>"Diterima" (Y)</strong>, sistem melalui <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded text-primary-600 dark:text-primary-400">SiswaDataPendaftarObserver</code> akan melakukan hal berikut secara otomatis:</p>

    <ol class="list-decimal list-inside mb-6 space-y-3 ml-4 text-gray-700 dark:text-gray-300">
        <li><strong>Pembuatan Nomor Induk (NIM)</strong>: Sistem men-generate NIM otomatis berdasarkan tahun akademik dan urutan (Format: <code class="px-1 bg-gray-100 dark:bg-gray-800 rounded">TAHUN + 000XXX</code>).</li>
        <li><strong>Pembuatan Riwayat Pendidikan</strong>: Membuat data <code class="px-1 bg-gray-100 dark:bg-gray-800 rounded">RiwayatPendidikan</code> baru dengan status "Aktif".</li>
        <li>
            <strong>Perubahan Role User</strong>:
            <ul class="list-disc list-inside mt-2 ml-6 space-y-1">
                <li>Role <code class="px-1 bg-gray-100 dark:bg-gray-800 rounded">pendaftar</code> dihapus.</li>
                <li>Role <code class="px-1 bg-gray-100 dark:bg-gray-800 rounded">murid</code> ditambahkan.</li>
                <li>Pendaftar kini dapat mengakses menu sebagai Mahasiswa Aktif.</li>
            </ul>
        </li>
    </ol>

    <hr class="my-6 border-gray-200 dark:border-gray-700">

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span>📝</span> Referensi Kode Status
    </h2>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meaning</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                <tr>
                    <td class="px-4 py-3 text-sm font-medium" rowspan="3">Status_Pendaftaran</td>
                    <td class="px-4 py-3 text-sm text-center">B</td>
                    <td class="px-4 py-3 text-sm">Pending/Proses</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm text-center">Y</td>
                    <td class="px-4 py-3 text-sm text-center">Diterima</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm text-center">N</td>
                    <td class="px-4 py-3 text-sm">Ditolak</td>
                </tr>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                    <td class="px-4 py-3 text-sm font-medium" rowspan="3">Status_Kelulusan_Seleksi</td>
                    <td class="px-4 py-3 text-sm text-center">B</td>
                    <td class="px-4 py-3 text-sm">Proses</td>
                </tr>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                    <td class="px-4 py-3 text-sm text-center">Y</td>
                    <td class="px-4 py-3 text-sm">Lulus</td>
                </tr>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                    <td class="px-4 py-3 text-sm text-center">N</td>
                    <td class="px-4 py-3 text-sm">Tidak Lulus</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm font-medium" rowspan="2">status_valid</td>
                    <td class="px-4 py-3 text-sm text-center">0</td>
                    <td class="px-4 py-3 text-sm">Belum Divalidasi</td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-sm text-center">1</td>
                    <td class="px-4 py-3 text-sm">Sudah Divalidasi</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
