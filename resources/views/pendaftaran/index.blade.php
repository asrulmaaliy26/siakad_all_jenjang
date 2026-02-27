<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <!-- Tambahkan Alpine.js Collapse plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.3/dist/cdn.min.js"></script>
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
                @if($pengaturanPendaftaran->foto_header)
                <img src="{{ Storage::url($pengaturanPendaftaran->foto_header) }}" class="w-full h-full object-cover absolute md:static inset-0" alt="Wallpaper Pendaftaran">
                @else
                <img src="{{ asset('assets/wallpaper.jpg') }}" class="w-full h-full object-cover absolute md:static inset-0" alt="Wallpaper Pendaftaran">
                @endif
                <div class="absolute inset-0 bg-black bg-opacity-10 hover:bg-opacity-5 transition duration-500"></div>
            </div>
            <div class="md:w-1/3 p-6 bg-lime-50 text-lime-900 border-l-4 border-lime-500 flex flex-col justify-center relative justify-between">
                <div>
                    <h3 class="font-bold text-lg mb-3 flex items-center gap-2 text-lime-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Catatan Penting
                    </h3>
                    @if($pengaturanPendaftaran->deskripsi_pendaftaran)
                    <p class="text-sm text-lime-800 mb-4">{{ $pengaturanPendaftaran->deskripsi_pendaftaran }}</p>
                    @endif
                    <ul class="text-sm space-y-2 text-lime-800 list-disc list-inside mb-4">
                        <li>Isi data dengan <strong>Valid</strong>.</li>
                        <li>Siapkan <strong>Scan Dokumen</strong>.</li>
                        <li>(<span class="text-red-500">*</span>) Wajib diisi.</li>
                        <li>Pendaftaran: <strong>{{ $pengaturanPendaftaran->getGelombangAktif() }}</strong></li>
                    </ul>
                </div>

                @if($pengaturanPendaftaran->brosur_pendaftaran)
                <a href="{{ Storage::url($pengaturanPendaftaran->brosur_pendaftaran) }}" target="_blank" class="w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 mt-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Brosur
                </a>
                @endif

                <a href="{{ route('filament.admin.auth.login') }}" class="w-full inline-flex justify-center items-center py-2 px-4 border border-lime-600 shadow-sm text-sm font-medium rounded-md text-lime-700 bg-white hover:bg-lime-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 mt-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Sudah Punya Akun? Login
                </a>
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

            @if(!$pengaturanPendaftaran->isPendaftaranBuka())
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-4 text-center">
                <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="text-lg font-medium text-yellow-800">Pendaftaran Saat Ini Sedang Ditutup</h3>
                <p class="mt-2 text-sm text-yellow-700">Mohon maaf, saat ini pendaftaran mahasiswa baru sedang tidak aktif. Silakan kembali lagi nanti atau hubungi Admin untuk informasi lebih lanjut.</p>
            </div>
            @else

            <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data"
                x-data="{ 
                    activeStep: 1, 
                    selectedJurusanId: {{ json_encode(old('id_jurusan')) }},
                    jurusans: {{ json_encode($jurusans->map(fn($j) => ['id' => $j->id, 'nama' => $j->nama])) }}
                }">
                @csrf

                @if(isset($referalCode))
                <input type="hidden" name="id_referal_code" value="{{ $referalCode->id }}">
                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-purple-800">Anda Mendaftar Menggunakan Kode Referal</h3>
                            <div class="mt-1 text-sm text-purple-700">
                                <p>Pemberi Referal: <strong>{{ $referalCode->nama }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                <!-- TABS & CONTENT LAYOUT FUTURISTIK (TOP TABS) -->
                <div class="flex flex-col gap-6">
                    <!-- TOP TABS NAVIGATION -->
                    <div class="flex flex-nowrap items-center gap-2 md:gap-4 overflow-x-auto pb-4 pt-2 scrollbar-hide" style="-webkit-overflow-scrolling: touch; scroll-behavior: smooth;">
                        <template x-for="(stepName, index) in ['Akun & Prodi', 'Data Pribadi', 'Alamat & Kontak', 'Sekolah Asal', 'Data Orang Tua', 'Upload Dokumen']">
                            <button type="button" @click="activeStep = index + 1"
                                x-init="$watch('activeStep', val => { if(val === index + 1) { setTimeout(() => $el.scrollIntoView({behavior: 'smooth', block: 'nearest', inline: 'center'}), 50) } })"
                                :class="activeStep === (index + 1) ? 'bg-lime-600 text-white shadow-lg transform scale-105 border-transparent z-10' : 'bg-white text-gray-600 hover:bg-lime-50 border-gray-200 hover:border-lime-300'"
                                class="flex-shrink-0 px-4 py-3 rounded-xl font-bold text-center transition-all duration-300 flex flex-col md:flex-row items-center justify-center gap-2 group border">
                                <span :class="activeStep === (index + 1) ? 'bg-white text-lime-600' : 'bg-gray-100 text-gray-500 group-hover:bg-lime-200 group-hover:text-lime-700'" class="flex items-center justify-center w-6 h-6 md:w-8 md:h-8 rounded-full text-xs md:text-sm transition-colors duration-300 shadow-sm" x-text="index + 1"></span>
                                <span class="text-xs md:text-sm whitespace-nowrap" x-text="stepName"></span>
                            </button>
                        </template>
                    </div>

                    <!-- CONTENT AREA BAWAH -->
                    <div class="w-full bg-white p-6 md:p-8 rounded-2xl shadow-xl border border-gray-100 min-h-[500px] relative overflow-hidden">

                        <!-- TAB 1 CONTENT -->
                        <div x-show="activeStep === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Akun & Program Studi</h2>
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
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700" for="ro_program_sekolah">Program Sekolah <span class="text-red-500">*</span></label>
                                            <select name="ro_program_sekolah" id="ro_program_sekolah" class="filament-input mt-1" required>
                                                <option value="">-- Pilih Program --</option>
                                                @foreach($programSekolahs as $program)
                                                <option value="{{ $program->id }}" {{ old('ro_program_sekolah') == $program->id ? 'selected' : '' }}>{{ $program->nilai }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm text-gray-700" for="id_jurusan">Pilihan Jurusan <span class="text-red-500">*</span></label>
                                            <select name="id_jurusan" id="id_jurusan" class="filament-input mt-1" x-model="selectedJurusanId" required>
                                                <option value="">-- Pilih Jurusan --</option>
                                                <template x-for="jurusan in jurusans">
                                                    <option :value="jurusan.id" x-text="jurusan.nama" :selected="jurusan.id == selectedJurusanId"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- Kelas Program (Visible for ALL) -->
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700" for="Kelas_Program_Kuliah">Kelas Program</label>
                                            <select name="Kelas_Program_Kuliah" id="Kelas_Program_Kuliah" class="filament-input mt-1">
                                                <option value="Reguler Pagi" {{ old('Kelas_Program_Kuliah') == 'Reguler Pagi' ? 'selected' : '' }}>Reguler Pagi</option>
                                                <option value="Reguler Sore" {{ old('Kelas_Program_Kuliah') == 'Reguler Sore' ? 'selected' : '' }}>Reguler Sore</option>
                                                <option value="Karyawan" {{ old('Kelas_Program_Kuliah') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block font-medium text-sm text-gray-700" for="Jalur_PMB">Jalur Pendaftaran <span class="text-red-500">*</span></label>
                                            <select name="Jalur_PMB" id="Jalur_PMB" class="filament-input mt-1" required>
                                                <option value="">-- Pilih Jalur --</option>
                                                @foreach($jalurPmbs as $jalur)
                                                <option value="{{ $jalur->id }}" {{ old('Jalur_PMB') == $jalur->id ? 'selected' : '' }}>{{ $jalur->nilai }} - {{ $jalur->deskripsi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block font-medium text-sm text-gray-700" for="Jenis_Pembiayaan">Rencana Pembiayaan</label>
                                            <select name="Jenis_Pembiayaan" id="Jenis_Pembiayaan" class="filament-input mt-1">
                                                <option value="Mandiri" {{ old('Jenis_Pembiayaan') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                                <option value="Orang Tua" {{ old('Jenis_Pembiayaan') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                                                <option value="Beasiswa" {{ old('Jenis_Pembiayaan') == 'Beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                                                <option value="Lainnya" {{ old('Jenis_Pembiayaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <button type="button" @click="activeStep = 2" class="px-6 py-2 bg-lime-600 text-white rounded-lg hover:bg-lime-700 text-sm font-semibold shadow-md transition-transform transform hover:-translate-y-0.5">Lanjut ke Data Pribadi &rarr;</button>
                            </div>
                        </div>

                        <!-- TAB 2 CONTENT -->
                        <div x-show="activeStep === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Data Pribadi</h2>
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
                            <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <button type="button" @click="activeStep = 1" class="px-5 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm transition-all">&larr; Sebelumnya</button>
                                <button type="button" @click="activeStep = 3" class="px-6 py-2 bg-lime-600 text-white rounded-lg hover:bg-lime-700 text-sm font-semibold shadow-md transition-transform transform hover:-translate-y-0.5">Lanjut ke Alamat &rarr;</button>
                            </div>
                        </div>

                        <!-- TAB 3 CONTENT -->
                        <div x-show="activeStep === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Alamat & Kontak</h2>
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
                            <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <button type="button" @click="activeStep = 2" class="px-5 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm transition-all">&larr; Sebelumnya</button>
                                <button type="button" @click="activeStep = 4" class="px-6 py-2 bg-lime-600 text-white rounded-lg hover:bg-lime-700 text-sm font-semibold shadow-md transition-transform transform hover:-translate-y-0.5">Lanjut ke Sekolah &rarr;</button>
                            </div>
                        </div>

                        <!-- TAB 4 CONTENT -->
                        <div x-show="activeStep === 4" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Sekolah Asal & Data Pindahan</h2>
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

                            <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <button type="button" @click="activeStep = 3" class="px-5 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm transition-all">&larr; Sebelumnya</button>
                                <button type="button" @click="activeStep = 5" class="px-6 py-2 bg-lime-600 text-white rounded-lg hover:bg-lime-700 text-sm font-semibold shadow-md transition-transform transform hover:-translate-y-0.5">Lanjut ke Data Orang Tua &rarr;</button>
                            </div>
                        </div>

                        <!-- TAB 5 CONTENT -->
                        <div x-show="activeStep === 5" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Data Orang Tua / Wali</h2>

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

                            <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <button type="button" @click="activeStep = 4" class="px-5 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm transition-all">&larr; Sebelumnya</button>
                                <button type="button" @click="activeStep = 6" class="px-6 py-2 bg-lime-600 text-white rounded-lg hover:bg-lime-700 text-sm font-semibold shadow-md transition-transform transform hover:-translate-y-0.5">Lanjut ke Dokumen &rarr;</button>
                            </div>
                        </div>

                        <!-- TAB 6 CONTENT -->
                        <div x-show="activeStep === 6" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Upload Dokumen & Berkas</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="Legalisir_Ijazah">Scan Legalisir Ijazah</label>
                                    <input type="file" name="Legalisir_Ijazah[]" id="Legalisir_Ijazah" class="filament-input mt-1 pt-2" multiple>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="Legalisir_SKHU">Scan Legalisir SKHU</label>
                                    <input type="file" name="Legalisir_SKHU[]" id="Legalisir_SKHU" class="filament-input mt-1 pt-2" multiple>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="Copy_KTP">Scan Copy KTP</label>
                                    <input type="file" name="Copy_KTP[]" id="Copy_KTP" class="filament-input mt-1 pt-2" multiple>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="Foto_BW_3x3">Pas Foto Hitam Putih (3x3)</label>
                                    <input type="file" name="Foto_BW_3x3[]" id="Foto_BW_3x3" class="filament-input mt-1 pt-2" multiple accept="image/*">
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="Foto_BW_3x4">Pas Foto Hitam Putih (3x4)</label>
                                    <input type="file" name="Foto_BW_3x4[]" id="Foto_BW_3x4" class="filament-input mt-1 pt-2" multiple accept="image/*">
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="Foto_Warna_5x6">Pas Foto Warna (5x6)</label>
                                    <input type="file" name="Foto_Warna_5x6[]" id="Foto_Warna_5x6" class="filament-input mt-1 pt-2" multiple accept="image/*">
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700" for="File_Foto_Berwarna">Pas Foto Berwarna Selain 5x6</label>
                                    <input type="file" name="File_Foto_Berwarna[]" id="File_Foto_Berwarna" class="filament-input mt-1 pt-2" multiple accept="image/*">
                                    <span class="text-xs text-gray-500">Format: JPG/PNG, Max: 2MB</span>
                                </div>

                            </div>

                            <div class="mt-8 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <button type="button" @click="activeStep = 5" class="px-5 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm transition-all">&larr; Sebelumnya</button>

                                <button type="submit" class="px-8 py-3 bg-lime-600 text-white rounded-lg hover:bg-lime-700 text-base font-bold shadow-xl transform transition hover:-translate-y-1 uppercase tracking-wide animate-pulse">
                                    Simpan Pendaftaran &rarr;
                                </button>
                            </div>
                        </div>

                    </div> <!-- End Main Content Right -->
                </div> <!-- End Flex Split Layout -->
            </form>
            @endif
        </div> <!-- Tutup div class="w-full max-w-4xl mt-4 px-6 py-8 bg-white..." -->

        <div class="mb-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Sistem Informasi Akademik. All rights reserved.
        </div>
    </div> <!-- Tutup div class="min-h-screen..." -->

</body>

</html>