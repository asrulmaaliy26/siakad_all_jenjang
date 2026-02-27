<x-filament-panels::page>
    <div class="space-y-6">
        @if(!$riwayatAktif)
        <div class="p-6 bg-red-100 text-red-700 rounded-xl">
            Data akademik aktif tidak ditemukan. Silakan hubungi bagian akademik.
        </div>
        @else
        <!-- Header Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Status Yudisium</h3>
                <p class="mt-2 text-2xl font-bold {{ $riwayatAktif->status === 'Lulus' ? 'text-green-600' : 'text-gray-400' }}">
                    {{ $riwayatAktif->status === 'Lulus' ? 'LULUS' : 'BELUM LULUS' }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Judul Tugas Akhir</h3>
                <p class="mt-2 text-sm font-medium {{ $riwayatAktif->judul_skripsi ? 'text-gray-900 dark:text-gray-100' : 'text-yellow-600' }}">
                    {{ $riwayatAktif->judul_skripsi ?: 'Belum Diisi' }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Status Pendaftaran</h3>
                <p class="mt-2 text-lg font-bold">
                    @if($wisudaData)
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">TERDAFTAR PERIODE {{ $wisudaData->periodeWisuda?->periode_ke }}</span>
                    @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">BELUM MENDAFTAR</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Bebas Tanggungan Section -->
        <x-filament::section>
            <x-slot name="heading">Bebas Tanggungan</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                @foreach($this->getClearanceStatus() as $key => $status)
                <div class="p-5 border rounded-xl {{ $status['met'] ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
                    <div class="flex justify-between items-start">
                        <h4 class="font-bold text-gray-800 dark:text-gray-100">{{ $status['title'] }}</h4>
                        @if($status['met'])
                        <span class="text-green-600 dark:text-green-400 flex items-center gap-1 text-sm font-bold">
                            <x-filament::icon icon="heroicon-m-check-circle" class="w-5 h-5" />
                            Terpenuhi
                        </span>
                        @else
                        <span class="text-gray-400 dark:text-gray-500 flex items-center gap-1 text-sm">
                            <x-filament::icon icon="heroicon-m-x-circle" class="w-5 h-5" />
                            Belum Terpenuhi
                        </span>
                        @endif
                    </div>
                    <ul class="mt-3 space-y-1">
                        @foreach($status['points'] as $point)
                        <li class="text-xs text-gray-600 dark:text-gray-400 flex items-start gap-2">
                            <span class="mt-1 w-1 h-1 bg-gray-400 dark:bg-gray-500 rounded-full shrink-0"></span>
                            {{ $point }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Tabel Kuota Wisuda -->
        <x-filament::section>
            <x-slot name="heading">Total Wisudawan Tahun {{ date('Y') }}</x-slot>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm text-left border dark:border-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-2 border dark:border-gray-700 text-gray-700 dark:text-gray-300">Tahun / Periode</th>
                            @foreach($periodeWisudas as $p)
                            <th class="px-4 py-2 border dark:border-gray-700 text-center text-gray-700 dark:text-gray-300">Periode {{ $p->periode_ke }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 border dark:border-gray-700 font-medium text-gray-900 dark:text-gray-100">Pendaftar Wisuda</td>
                            @foreach($periodeWisudas as $p)
                            <td class="px-4 py-2 border dark:border-gray-700 text-center">
                                @if($p->status === 'Buka')
                                <div class="font-bold text-lime-600 dark:text-lime-500">{{ $p->pendaftar_count }}</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400">Kuota: {{ $p->kuota }}</div>
                                @elseif($p->status === 'Tutup')
                                <span class="text-red-500 dark:text-red-400 font-bold">Penuh</span>
                                @else
                                <span class="text-gray-400 dark:text-gray-500 text-[10px]">Belum Dibuka</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 italic">
                * Kuota setiap periode wisuda sejumlah 800 wisudawan. Waktu pelaksanaan akan diumumkan setelah kuota terpenuhi.
            </p>
        </x-filament::section>

        <!-- Form Pendaftaran -->
        @if($wisudaData && $wisudaData->status_pendaftaran === 'Disetujui')
        <div class="p-8 bg-green-600 dark:bg-green-700 text-white rounded-2xl shadow-xl text-center">
            <x-filament::icon icon="heroicon-o-check-badge" class="w-16 h-16 mx-auto mb-4" />
            <h2 class="text-2xl font-bold">Sukses Menyelesaikan Pendaftaran</h2>
            <p class="mt-2 opacity-90">Anda terdaftar wisuda periode {{ $wisudaData->periodeWisuda?->periode_ke }} tahun {{ $wisudaData->periodeWisuda?->tahun }}</p>
        </div>
        @else
        <x-filament::section>
            <x-slot name="heading">Formulir Pendaftaran Wisuda</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4 mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                    <span class="text-gray-500 dark:text-gray-400">NIM</span>
                    <span class="font-bold dark:text-gray-200">{{ $riwayatAktif->nomor_induk }}</span>
                </div>
                <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                    <span class="text-gray-500 dark:text-gray-400">Nama Lengkap</span>
                    <span class="font-bold uppercase dark:text-gray-200">{{ $riwayatAktif->siswaData->nama }}</span>
                </div>
                <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                    <span class="text-gray-500 dark:text-gray-400">Jenis Kelamin</span>
                    <span class="font-bold dark:text-gray-200">{{ $riwayatAktif->siswaData->jenis_kelamin }}</span>
                </div>
                <div class="flex justify-between border-b dark:border-gray-700 pb-2">
                    <span class="text-gray-500 dark:text-gray-400">Tempat Tanggal Lahir</span>
                    <span class="font-bold uppercase dark:text-gray-200">{{ $riwayatAktif->siswaData->kota_lahir }}, {{ \Carbon\Carbon::parse($riwayatAktif->siswaData->tanggal_lahir)->format('d F Y') }}</span>
                </div>
                <div class="flex justify-between border-b dark:border-gray-700 pb-2 md:col-span-2">
                    <span class="text-gray-500 dark:text-gray-400">Jurusan / Program Studi</span>
                    <span class="font-bold uppercase dark:text-gray-200">{{ $riwayatAktif->jurusan?->nama }}</span>
                </div>
            </div>

            <form wire:submit.prevent="submit" class="space-y-6">
                {{ $this->form }}

                <div class="flex justify-end gap-3 pt-6 border-t dark:border-gray-800">
                    <x-filament::button type="submit" size="lg" color="success">
                        {{ $wisudaData ? 'Perbarui Data Pendaftaran' : 'Daftar Wisuda Sekarang' }}
                    </x-filament::button>
                </div>
            </form>

            <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 text-xs rounded-lg border border-yellow-100 dark:border-yellow-800/50">
                <h5 class="font-bold mb-1 uppercase">Keterangan :</h5>
                <ul class="list-disc ml-4 space-y-1">
                    <li>Silahkan menghubungi BAAK jika biodata Anda tidak sesuai</li>
                    <li>Pas Foto yang diunggah harus sesuai standar ijazah untuk dicetak di buku wisuda</li>
                </ul>
            </div>
        </x-filament::section>
        @endif
        @endif
    </div>
</x-filament-panels::page>