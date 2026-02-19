<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
        }

        .filament-input {
            width: 100%;
            border-color: #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transition: all 0.15s ease-in-out;
        }

        .filament-input:focus {
            outline: none;
            border-color: #65a30d;
            /* Lime-600 for Filament-like feel (Primary) */
            box-shadow: 0 0 0 2px rgba(101, 163, 13, 0.2);
        }

        .accordion-item {
            background: white;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .accordion-header {
            cursor: pointer;
            padding: 1rem 1.5rem;
            background-color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #111827;
            transition: background-color 0.2s;
        }

        .accordion-header:hover {
            background-color: #f9fafb;
        }

        .accordion-body {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            background-color: #fff;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
</head>

<body class="text-gray-900 antialiased">

    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100 pb-12">

        <!-- HEADER IMAGE & NOTES -->
        <div class="w-full max-w-4xl mt-6 bg-white shadow-xl overflow-hidden sm:rounded-lg flex flex-col md:flex-row">
            <div class="md:w-2/3 relative min-h-[250px] md:min-h-full">
                <img src="{{ asset('assets/wallpaper.jpg') }}" class="w-full h-full object-cover absolute md:static inset-0" alt="Wallpaper Pendaftaran">
                <div class="absolute inset-0 bg-black bg-opacity-10 hover:bg-opacity-5 transition duration-500"></div>
            </div>
            <div class="md:w-1/3 p-6 bg-lime-50 text-lime-900 border-l-4 border-lime-500 flex flex-col justify-center relative">
                <h3 class="font-bold text-lg mb-3 flex items-center gap-2 text-lime-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Catatan Penting
                </h3>
                <ul class="text-sm space-y-2 text-lime-800 list-disc list-inside">
                    <li>Isi data dengan <strong>Valid</strong>.</li>
                    <li>Siapkan <strong>Scan Dokumen</strong>.</li>
                    <li>(<span class="text-red-500">*</span>) Wajib diisi.</li>
                    <li>Cek email secara berkala.</li>
                </ul>
            </div>
        </div>

        <div class="w-full max-w-4xl mt-4 px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-lg mb-10">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-lime-600 tracking-tight text-gray-900">Form Pendaftaran Mahasiswa Baru</h1>
                <p class="mt-2 text-sm text-gray-600">Silakan lengkapi data diri Anda dibawah ini.</p>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Gagal Menyimpan Data!</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data"
                x-data="{ 
                    activeStep: 1, 
                    selectedJenjangName: {{ json_encode(old('id_jenjang_pendidikan') ? $jenjangs->find(old('id_jenjang_pendidikan'))?->nama : '') }}, 
                    selectedJenjangId: {{ json_encode(old('id_jenjang_pendidikan')) }},
                    selectedJurusanId: {{ json_encode(old('id_jurusan')) }},
                    jurusans: {{ json_encode($jurusans->map(fn($j) => ['id' => $j->id, 'nama' => $j->nama, 'id_jenjang_pendidikan' => $j->id_jenjang_pendidikan])) }}
                }">
                @csrf

                <!-- STANDOUT JENJANG PENDIDIKAN SELECTION -->
                <div class="bg-yellow-50 border-l-8 border-yellow-400 p-6 mb-8 rounded-r-lg shadow-sm">
                    <label class="block text-lg font-bold text-yellow-800 mb-2" for="id_jenjang_pendidikan">
                        PILIH JENJANG PENDIDIKAN TERLEBIH DAHULU
                        <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-yellow-700 mb-4">Pastikan Anda memilih jenjang pendidikan yang benar sebelum mengisi data lainnya.</p>
                    <select name="id_jenjang_pendidikan" id="id_jenjang_pendidikan" x-model="selectedJenjangId"
                        @change="selectedJenjangName = $event.target.options[$event.target.selectedIndex].dataset.nama"
                        class="filament-input mt-1 border-yellow-300 focus:border-yellow-500 focus:ring-yellow-500 text-lg py-3 bg-white" required>
                        <option value="" data-nama="">-- Silakan Pilih Jenjang Pendidikan --</option>
                        @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang->id }}" data-nama="{{ $jenjang->nama }}" {{ old('id_jenjang_pendidikan') == $jenjang->id ? 'selected' : '' }}>
                            {{ $jenjang->nama }} {{ $jenjang->deskripsi ? '('.$jenjang->deskripsi.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- ACCORDION 1: AKUN & PROGRAM STUDI -->
                <div class="accordion-item">
                    <div class="accordion-header" @click="activeStep === 1 ? activeStep = null : activeStep = 1">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-lime-100 text-lime-700 font-bold text-sm">1</span>
                            <span>Akun & Program Studi</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform" :class="activeStep === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="accordion-body" x-show="activeStep === 1" x-collapse>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Akun -->
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Login Akun</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="nama">Nama <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama" id="nama" class="filament-input mt-1" value="{{ old('nama') }}" required placeholder="Nama lengkap Anda">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="username">Username <span class="text-red-500">*</span></label>
                                        <input type="text" name="username" id="username" class="filament-input mt-1" value="{{ old('username') }}" required placeholder="Username untuk login">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="password">Password <span class="text-red-500">*</span></label>
                                        <input type="password" name="password" id="password" class="filament-input mt-1" required>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="password_confirmation">Konfirmasi Password <span class="text-red-500">*</span></label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="filament-input mt-1" required>
                                    </div>
                                </div>
                            </div>

                            <hr class="md:col-span-2 border-gray-200 my-2">

                            <!-- Program Studi -->
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Pilihan Program Studi</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Jenjang Pendidikan Moved to Top -->

                                    <!-- Program Sekolah (Visible for ALL) -->
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="ro_program_sekolah">Program Sekolah</label>
                                        <select name="ro_program_sekolah" id="ro_program_sekolah" class="filament-input mt-1">
                                            <option value="">-- Pilih Program --</option>
                                            @foreach($programSekolahs as $program)
                                            <option value="{{ $program->id }}" {{ old('ro_program_sekolah') == $program->id ? 'selected' : '' }}>{{ $program->nilai }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Jurusan (Visible for ALL) -->
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="id_jurusan">Pilihan Jurusan</label>
                                        <select name="id_jurusan" id="id_jurusan" class="filament-input mt-1" x-model="selectedJurusanId">
                                            <option value="">-- Pilih Jurusan --</option>
                                            <template x-for="jurusan in jurusans.filter(j => j.id_jenjang_pendidikan == selectedJenjangId)">
                                                <option :value="jurusan.id" x-text="jurusan.nama" :selected="jurusan.id == selectedJurusanId"></option>
                                            </template>
                                            <template x-if="selectedJenjangId && jurusans.filter(j => j.id_jenjang_pendidikan == selectedJenjangId).length === 0">
                                                <option disabled>-- Tidak ada jurusan untuk jenjang ini --</option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Kelas Program (Visible for ALL) -->
                                    <!-- <div x-show="selectedJenjangName !== 'MA'"></div> -->
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="Kelas_Program_Kuliah">Kelas Program</label>
                                        <select name="Kelas_Program_Kuliah" id="Kelas_Program_Kuliah" class="filament-input mt-1">
                                            <option value="Reguler Pagi" {{ old('Kelas_Program_Kuliah') == 'Reguler Pagi' ? 'selected' : '' }}>Reguler Pagi</option>
                                            <option value="Reguler Sore" {{ old('Kelas_Program_Kuliah') == 'Reguler Sore' ? 'selected' : '' }}>Reguler Sore</option>
                                            <option value="Karyawan" {{ old('Kelas_Program_Kuliah') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="Jalur_PMB">Jalur Pendaftaran</label>
                                        <select name="Jalur_PMB" id="Jalur_PMB" class="filament-input mt-1">
                                            <option value="">-- Pilih Jalur --</option>
                                            @foreach($jalurPmbs as $jalur)
                                            <option value="{{ $jalur->id }}" {{ old('Jalur_PMB') == $jalur->id ? 'selected' : '' }}>{{ $jalur->nilai }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="Jenis_Pembiayaan">Rencana Pembiayaan</label>
                                        <select name="Jenis_Pembiayaan" id="Jenis_Pembiayaan" class="filament-input mt-1">
                                            <option value="Mandiri" {{ old('Jenis_Pembiayaan') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                            <option value="Beasiswa" {{ old('Jenis_Pembiayaan') == 'Beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                                            <option value="Lainnya" {{ old('Jenis_Pembiayaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="button" @click="activeStep = 2" class="px-4 py-2 bg-lime-600 text-white rounded-md hover:bg-lime-700 text-sm font-medium">Lanjut ke Data Pribadi &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- ACCORDION 2: DATA PRIBADI -->
                <div class="accordion-item">
                    <div class="accordion-header" @click="activeStep === 2 ? activeStep = null : activeStep = 2">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-lime-100 text-lime-700 font-bold text-sm">2</span>
                            <span>Data Pribadi</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform" :class="activeStep === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="accordion-body" x-show="activeStep === 2" x-collapse>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="nama_lengkap">Nama Lengkap (Sesuai Ijazah)</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="filament-input mt-1" placeholder="email@example.com">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="jenis_kelamin">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="filament-input mt-1">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir') }}" class="filament-input mt-1">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="filament-input mt-1">
                                </div>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="agama">Agama</label>
                                <select name="agama" id="agama" class="filament-input mt-1">
                                    <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                    <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Budha" {{ old('agama') == 'Budha' ? 'selected' : '' }}>Budha</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="golongan_darah">Golongan Darah</label>
                                <select name="golongan_darah" id="golongan_darah" class="filament-input mt-1">
                                    <option value="">-- Pilih --</option>
                                    <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="no_ktp">NIK / No. KTP</label>
                                <input type="text" name="no_ktp" id="no_ktp" value="{{ old('no_ktp') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="no_kk">No. KK</label>
                                <input type="text" name="no_kk" id="no_kk" value="{{ old('no_kk') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="kebutuhan_khusus">Kebutuhan Khusus</label>
                                <input type="text" name="kebutuhan_khusus" id="kebutuhan_khusus" value="{{ old('kebutuhan_khusus') }}" class="filament-input mt-1" placeholder="Kosongkan jika tidak ada">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between">
                            <button type="button" @click="activeStep = 1" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">&larr; Sebelumnya</button>
                            <button type="button" @click="activeStep = 3" class="px-4 py-2 bg-lime-600 text-white rounded-md hover:bg-lime-700 text-sm font-medium">Lanjut ke Alamat &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- ACCORDION 3: ALAMAT & KONTAK -->
                <div class="accordion-item">
                    <div class="accordion-header" @click="activeStep === 3 ? activeStep = null : activeStep = 3">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-lime-100 text-lime-700 font-bold text-sm">3</span>
                            <span>Alamat & Kontak</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform" :class="activeStep === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="accordion-body" x-show="activeStep === 3" x-collapse>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700" for="alamat">Alamat Lengkap (Jalan, Gg, Blok)</label>
                                <textarea name="alamat" id="alamat" rows="2" class="filament-input mt-1" placeholder="Contoh: Jl. Merdeka No. 10">{{ old('alamat') }}</textarea>
                            </div>
                            <div class="grid grid-cols-3 gap-4 md:col-span-2">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="rt">RT</label>
                                    <input type="text" name="rt" id="rt" value="{{ old('rt') }}" class="filament-input mt-1">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="rw">RW</label>
                                    <input type="text" name="rw" id="rw" value="{{ old('rw') }}" class="filament-input mt-1">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="nomor_rumah">No. Rumah</label>
                                    <input type="text" name="nomor_rumah" id="nomor_rumah" value="{{ old('nomor_rumah') }}" class="filament-input mt-1">
                                </div>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="dusun">Dusun / Lingkungan</label>
                                <input type="text" name="dusun" id="dusun" value="{{ old('dusun') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="desa">Desa / Kelurahan</label>
                                <input type="text" name="desa" id="desa" value="{{ old('desa') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="kecamatan">Kecamatan</label>
                                <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="kabupaten">Kabupaten / Kota</label>
                                <input type="text" name="kabupaten" id="kabupaten" value="{{ old('kabupaten') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="provinsi">Provinsi</label>
                                <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="kode_pos">Kode Pos</label>
                                <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="no_telepon">No. Handphone / WA</label>
                                <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="jenis_domisili">Status Tempat Tinggal</label>
                                <select name="jenis_domisili" id="jenis_domisili" class="filament-input mt-1">
                                    <option value="Rumah Orang Tua" {{ old('jenis_domisili') == 'Rumah Orang Tua' ? 'selected' : '' }}>Rumah Orang Tua</option>
                                    <option value="Rumah Sendiri" {{ old('jenis_domisili') == 'Rumah Sendiri' ? 'selected' : '' }}>Rumah Sendiri</option>
                                    <option value="Kost" {{ old('jenis_domisili') == 'Kost' ? 'selected' : '' }}>Kost</option>
                                    <option value="Asrama" {{ old('jenis_domisili') == 'Asrama' ? 'selected' : '' }}>Asrama</option>
                                    <option value="Wali" {{ old('jenis_domisili') == 'Wali' ? 'selected' : '' }}>Wali</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between">
                            <button type="button" @click="activeStep = 2" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">&larr; Sebelumnya</button>
                            <button type="button" @click="activeStep = 4" class="px-4 py-2 bg-lime-600 text-white rounded-md hover:bg-lime-700 text-sm font-medium">Lanjut ke Sekolah &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- ACCORDION 4: SEKOLAH ASAL & MUTASI -->
                <div class="accordion-item">
                    <div class="accordion-header" @click="activeStep === 4 ? activeStep = null : activeStep = 4">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-lime-100 text-lime-700 font-bold text-sm">4</span>
                            <span>Sekolah Asal & Data Pindahan</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform" :class="activeStep === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="accordion-body" x-show="activeStep === 4" x-collapse>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Sekolah Asal</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="asal_slta">Nama Sekolah Asal</label>
                                <input type="text" name="asal_slta" id="asal_slta" value="{{ old('asal_slta') }}" class="filament-input mt-1" placeholder="SMA Negeri 1...">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="status_asal_sekolah">Status Sekolah</label>
                                <select name="status_asal_sekolah" id="status_asal_sekolah" class="filament-input mt-1">
                                    <option value="Negeri" {{ old('status_asal_sekolah') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                    <option value="Swasta" {{ old('status_asal_sekolah') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="jenis_slta">Jenis Sekolah</label>
                                <select name="jenis_slta" id="jenis_slta" class="filament-input mt-1">
                                    <option value="SMA">SMA</option>
                                    <option value="SMK">SMK</option>
                                    <option value="MA">MA</option>
                                    <option value="MAK">MAK</option>
                                    <option value="Pondok">Pondok Pesantren</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="kejuruan_slta">Jurusan / Peminatan</label>
                                <input type="text" name="kejuruan_slta" id="kejuruan_slta" value="{{ old('kejuruan_slta') }}" class="filament-input mt-1" placeholder="IPA/IPS/TKJ...">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="tahun_lulus_slta">Tahun Lulus</label>
                                <input type="number" name="tahun_lulus_slta" id="tahun_lulus_slta" value="{{ old('tahun_lulus_slta') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="nisn">NISN</label>
                                <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" class="filament-input mt-1">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="nomor_seri_ijazah_slta">No. Seri Ijazah</label>
                                <input type="text" name="nomor_seri_ijazah_slta" id="nomor_seri_ijazah_slta" value="{{ old('nomor_seri_ijazah_slta') }}" class="filament-input mt-1">
                            </div>
                        </div>

                        <div x-data="{ isTransfer: false }" x-show="selectedJenjangName !== 'MA'">
                            <div class="flex items-center mb-4">
                                <input id="is_transfer" type="checkbox" x-model="isTransfer" class="rounded border-gray-300 text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50">
                                <label for="is_transfer" class="ml-2 block text-sm text-gray-900 font-medium">Saya Mahasiswa Pindahan / Transfer</label>
                            </div>

                            <div x-show="isTransfer" x-collapse>
                                <hr class="my-4 border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Kampus Asal (Pindahan)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="PT_Asal">Perguruan Tinggi Asal</label>
                                        <input type="text" name="PT_Asal" id="PT_Asal" value="{{ old('PT_Asal') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="Prodi_Asal">Prodi Asal</label>
                                        <input type="text" name="Prodi_Asal" id="Prodi_Asal" value="{{ old('Prodi_Asal') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="NIMKO_Asal">NIM Asal</label>
                                        <input type="text" name="NIMKO_Asal" id="NIMKO_Asal" value="{{ old('NIMKO_Asal') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="IPK_Asal">IPK Terakhir</label>
                                        <input type="text" name="IPK_Asal" id="IPK_Asal" value="{{ old('IPK_Asal') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700" for="Jml_SKS_Asal">Jumlah SKS Diakui</label>
                                        <input type="number" name="Jml_SKS_Asal" id="Jml_SKS_Asal" value="{{ old('Jml_SKS_Asal') }}" class="filament-input mt-1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between">
                            <button type="button" @click="activeStep = 3" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">&larr; Sebelumnya</button>
                            <button type="button" @click="activeStep = 5" class="px-4 py-2 bg-lime-600 text-white rounded-md hover:bg-lime-700 text-sm font-medium">Lanjut ke Data Orang Tua &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- ACCORDION 5: DATA ORANG TUA -->
                <div class="accordion-item">
                    <div class="accordion-header" @click="activeStep === 5 ? activeStep = null : activeStep = 5">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-lime-100 text-lime-700 font-bold text-sm">5</span>
                            <span>Data Orang Tua / Wali</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform" :class="activeStep === 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="accordion-body" x-show="activeStep === 5" x-collapse>

                        <!-- TAB FOR AYAH / IBU (Simple Layout) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- AYAH -->
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="font-bold text-gray-700 mb-4 border-b pb-2">Data Ayah</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Nama_Ayah">Nama Lengkap</label>
                                        <input type="text" name="Nama_Ayah" id="Nama_Ayah" value="{{ old('Nama_Ayah') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Nomor_KTP_Ayah">NIK / No. KTP</label>
                                        <input type="text" name="Nomor_KTP_Ayah" id="Nomor_KTP_Ayah" value="{{ old('Nomor_KTP_Ayah') }}" class="filament-input mt-1">
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block font-medium text-xs text-gray-600" for="Tempat_Lhr_Ayah">Tempat Lahir</label>
                                            <input type="text" name="Tempat_Lhr_Ayah" id="Tempat_Lhr_Ayah" value="{{ old('Tempat_Lhr_Ayah') }}" class="filament-input mt-1">
                                        </div>
                                        <div>
                                            <label class="block font-medium text-xs text-gray-600" for="Thn_Lhr_ayah">Tahun Lahir</label>
                                            <input type="text" name="Thn_Lhr_ayah" id="Thn_Lhr_ayah" value="{{ old('Thn_Lhr_ayah') }}" class="filament-input mt-1">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Pendidikan_Terakhir_Ayah">Pendidikan</label>
                                        <select name="Pendidikan_Terakhir_Ayah" id="Pendidikan_Terakhir_Ayah" class="filament-input mt-1">
                                            <option value="">-- Pilih --</option>
                                            <option value="SD">SD</option>
                                            <option value="SMP">SMP</option>
                                            <option value="SMA">SMA</option>
                                            <option value="S1">S1</option>
                                            <option value="S2">S2</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Pekerjaan_Ayah">Pekerjaan</label>
                                        <input type="text" name="Pekerjaan_Ayah" id="Pekerjaan_Ayah" value="{{ old('Pekerjaan_Ayah') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="No_HP_ayah">No. HP</label>
                                        <input type="text" name="No_HP_ayah" id="No_HP_ayah" value="{{ old('No_HP_ayah') }}" class="filament-input mt-1">
                                    </div>
                                </div>
                            </div>

                            <!-- IBU -->
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 class="font-bold text-gray-700 mb-4 border-b pb-2">Data Ibu</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Nama_Ibu">Nama Lengkap</label>
                                        <input type="text" name="Nama_Ibu" id="Nama_Ibu" value="{{ old('Nama_Ibu') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Nomor_KTP_Ibu">NIK / No. KTP</label>
                                        <input type="text" name="Nomor_KTP_Ibu" id="Nomor_KTP_Ibu" value="{{ old('Nomor_KTP_Ibu') }}" class="filament-input mt-1">
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block font-medium text-xs text-gray-600" for="Tempat_Lhr_Ibu">Tempat Lahir</label>
                                            <input type="text" name="Tempat_Lhr_Ibu" id="Tempat_Lhr_Ibu" value="{{ old('Tempat_Lhr_Ibu') }}" class="filament-input mt-1">
                                        </div>
                                        <div>
                                            <label class="block font-medium text-xs text-gray-600" for="Thn_Lhr_Ibu">Tahun Lahir</label>
                                            <input type="text" name="Thn_Lhr_Ibu" id="Thn_Lhr_Ibu" value="{{ old('Thn_Lhr_Ibu') }}" class="filament-input mt-1">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Pendidikan_Terakhir_Ibu">Pendidikan</label>
                                        <select name="Pendidikan_Terakhir_Ibu" id="Pendidikan_Terakhir_Ibu" class="filament-input mt-1">
                                            <option value="">-- Pilih --</option>
                                            <option value="SD">SD</option>
                                            <option value="SMP">SMP</option>
                                            <option value="SMA">SMA</option>
                                            <option value="S1">S1</option>
                                            <option value="S2">S2</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="Pekerjaan_Ibu">Pekerjaan</label>
                                        <input type="text" name="Pekerjaan_Ibu" id="Pekerjaan_Ibu" value="{{ old('Pekerjaan_Ibu') }}" class="filament-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-xs text-gray-600" for="No_HP_ibu">No. HP</label>
                                        <input type="text" name="No_HP_ibu" id="No_HP_ibu" value="{{ old('No_HP_ibu') }}" class="filament-input mt-1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between">
                            <button type="button" @click="activeStep = 4" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">&larr; Sebelumnya</button>
                            <button type="button" @click="activeStep = 6" class="px-4 py-2 bg-lime-600 text-white rounded-md hover:bg-lime-700 text-sm font-medium">Lanjut ke Dokumen &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- ACCORDION 6: DOKUMEN -->
                <div class="accordion-item">
                    <div class="accordion-header" @click="activeStep === 6 ? activeStep = null : activeStep = 6">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-lime-100 text-lime-700 font-bold text-sm">6</span>
                            <span>Upload Dokumen & Berkas</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform" :class="activeStep === 6 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="accordion-body" x-show="activeStep === 6" x-collapse>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="File_Foto_Berwarna">Pas Foto Berwarna (3x4)</label>
                                <input type="file" name="File_Foto_Berwarna" id="File_Foto_Berwarna" class="filament-input mt-1 pt-2">
                                <span class="text-xs text-gray-500">Format: JPG/PNG, Max: 2MB</span>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="nama_pendaftar">Nama Pendaftar (Wajib Diisi)</label>
                                <input type="text" name="nama_pendaftar" id="nama_pendaftar" value="{{ old('nama_pendaftar') }}" class="filament-input mt-1" placeholder="Nama orang yang mendaftarkan">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="Copy_KTP">Scan KTP</label>
                                <input type="file" name="Copy_KTP" id="Copy_KTP" class="filament-input mt-1 pt-2">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700" for="Legalisir_Ijazah">Scan Ijazah Terakhir</label>
                                <input type="file" name="Legalisir_Ijazah" id="Legalisir_Ijazah" class="filament-input mt-1 pt-2">
                            </div>

                        </div>

                        <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                            <button type="button" @click="activeStep = 5" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">&larr; Sebelumnya</button>

                            <button type="submit" class="px-6 py-3 bg-lime-600 text-white rounded-md hover:bg-lime-700 text-base font-bold shadow-lg transform transition hover:-translate-y-1">
                                SIMPAN PENDAFTARAN &rarr;
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <div class="mb-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Sistem Informasi Akademik. All rights reserved.
        </div>
    </div>
</body>

</html>