<?php

namespace App\Http\Controllers;

use App\Models\SiswaData;
use App\Models\SiswaDataOrangTua;
use App\Models\SiswaDataPendaftar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class PendaftaranController extends Controller
{
    /**
     * Display the registration form.
     */
    public function index(Request $request)
    {
        $jurusans = \App\Models\Jurusan::all();
        $jalurPmbs = \App\Models\ReferenceOption::where('nama_grup', 'jalur_pmb')->where('status', 1)->get();
        // Ambil reference option untuk program sekolah
        $programSekolahs = \App\Models\ReferenceOption::where('nama_grup', 'program_sekolah')->where('status', 1)->get();

        $referalCode = null;
        if ($request->has('ref')) {
            $referalCode = \App\Models\ReferalCode::where('kode', $request->query('ref'))->first();
        }

        $pengaturanPendaftaran = \App\Models\PengaturanPendaftaran::getAktif();

        return view('pendaftaran.index', compact('jurusans', 'jalurPmbs', 'programSekolahs', 'referalCode', 'pengaturanPendaftaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Pendaftaran store method called', [
            'username' => $request->username,
            'nama' => $request->nama,
        ]);

        $pengaturan = \App\Models\PengaturanPendaftaran::getAktif();
        if (!$pengaturan->isPendaftaranBuka()) {
            return back()->with('error', 'Pendaftaran sedang ditutup. Anda tidak dapat mengirim data.');
        }

        // Validation rules - HANYA 3 FIELD WAJIB: nama, username, password
        $validator = Validator::make($request->all(), [
            // REQUIRED FIELDS
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],

            // OPTIONAL FIELDS - Semua field lain nullable
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'nama_panggilan' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'string', 'max:10'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'agama' => ['nullable', 'string'],
            'email' => ['nullable', 'string', 'email', 'max:255'],

            // Parent Data (Ayah)
            'Nama_Ayah' => ['nullable', 'string', 'max:255'],
            'Tempat_Lhr_Ayah' => ['nullable', 'string', 'max:255'],
            'Tgl_Lhr_Ayah' => ['nullable', 'string', 'max:2'],
            'Bln_Lhr_Ayah' => ['nullable', 'string', 'max:2'],
            'Thn_Lhr_ayah' => ['nullable', 'string', 'max:4'],
            'Agama_Ayah' => ['nullable', 'string', 'max:50'],
            'Gol_Darah_Ayah' => ['nullable', 'string', 'max:5'],
            'Pendidikan_Terakhir_Ayah' => ['nullable', 'string', 'max:50'],
            'Pekerjaan_Ayah' => ['nullable', 'string', 'max:100'],
            'Penghasilan_Ayah' => ['nullable', 'string', 'max:100'],
            'Kebutuhan_Khusus_Ayah' => ['nullable', 'string', 'max:100'],
            'Nomor_KTP_Ayah' => ['nullable', 'string', 'max:20'],
            'Alamat_Ayah' => ['nullable', 'string'],
            'No_HP_ayah' => ['nullable', 'string', 'max:16'],

            // Parent Data (Ibu)
            'Nama_Ibu' => ['nullable', 'string', 'max:255'],
            'Tempat_Lhr_Ibu' => ['nullable', 'string', 'max:255'],
            'Tgl_Lhr_Ibu' => ['nullable', 'string', 'max:2'],
            'Bln_Lhr_Ibu' => ['nullable', 'string', 'max:2'],
            'Thn_Lhr_Ibu' => ['nullable', 'string', 'max:4'],
            'Agama_Ibu' => ['nullable', 'string', 'max:50'],
            'Gol_Darah_Ibu' => ['nullable', 'string', 'max:5'],
            'Pendidikan_Terakhir_Ibu' => ['nullable', 'string', 'max:50'],
            'Pekerjaan_Ibu' => ['nullable', 'string', 'max:100'],
            'Penghasilan_Ibu' => ['nullable', 'string', 'max:100'],
            'Kebutuhan_Khusus_Ibu' => ['nullable', 'string', 'max:100'],
            'Nomor_KTP_Ibu' => ['nullable', 'string', 'max:20'],
            'Alamat_Ibu' => ['nullable', 'string'],
            'No_HP_ibu' => ['nullable', 'string', 'max:16'],

            // Pendaftar Data (Extended)
            'nama_pendaftar' => ['nullable', 'string', 'max:255'],
            'Kelas_Program_Kuliah' => ['nullable', 'string', 'max:255'],
            'id_jurusan' => ['required', 'exists:jurusan,id'],
            'ro_program_sekolah' => ['required', 'exists:reference_option,id'],
            'Jalur_PMB' => ['required', 'exists:reference_option,id'], // ID Reference Option
            'Jenis_Pembiayaan' => ['nullable', 'string', 'max:255'],
            'id_referal_code' => ['nullable', 'exists:referal_codes,id'],
            // Transfer Data
            'NIMKO_Asal' => ['nullable', 'string', 'max:255'],
            'PT_Asal' => ['nullable', 'string', 'max:255'],
            'Prodi_Asal' => ['nullable', 'string', 'max:255'],
            'Jml_SKS_Asal' => ['nullable', 'integer'],
            'IPK_Asal' => ['nullable', 'string', 'max:255'],
            'Semester_Asal' => ['nullable', 'string', 'max:10'],
            'Pengantar_Mutasi' => ['nullable', 'string'],
            'Transkip_Asal' => ['nullable', 'string'],
            // Documents
            'Legalisir_Ijazah' => ['nullable', 'array'],
            'Legalisir_Ijazah.*' => ['file'],
            'Legalisir_SKHU' => ['nullable', 'array'],
            'Legalisir_SKHU.*' => ['file'],
            'Copy_KTP' => ['nullable', 'array'],
            'Copy_KTP.*' => ['file'],
            // Photos
            'File_Foto_Berwarna' => ['nullable', 'array'],
            'File_Foto_Berwarna.*' => ['file', 'image'],
            'Foto_BW_3x3' => ['nullable', 'array'],
            'Foto_BW_3x3.*' => ['file', 'image'],
            'Foto_BW_3x4' => ['nullable', 'array'],
            'Foto_BW_3x4.*' => ['file', 'image'],
            'Foto_Warna_5x6' => ['nullable', 'array'],
            'Foto_Warna_5x6.*' => ['file', 'image'],
            'Nama_File_Foto' => ['nullable', 'string', 'max:255'],

            // Additional Siswa Fields
            'golongan_darah' => ['nullable', 'string', 'max:5'],
            'nomor_rumah' => ['nullable', 'string', 'max:20'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'desa' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'tempat_domisili' => ['nullable', 'string', 'max:255'],
            'jenis_domisili' => ['nullable', 'string', 'max:50'],
            'no_ktp' => ['nullable', 'string', 'max:20'],
            'no_kk' => ['nullable', 'string', 'max:20'],
            'kewarganegaraan' => ['nullable', 'string', 'max:50'],
            'anak_ke' => ['nullable', 'integer'],
            'jumlah_saudara' => ['nullable', 'integer'],
            'asal_slta' => ['nullable', 'string', 'max:100'],
            'status_asal_sekolah' => ['nullable', 'in:Negeri,Swasta'],
            'jenis_slta' => ['nullable', 'string', 'max:50'],
            'kejuruan_slta' => ['nullable', 'string', 'max:100'],
            'tahun_lulus_slta' => ['nullable', 'integer'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'nomor_seri_ijazah_slta' => ['nullable', 'string', 'max:50'],
        ], [
            // Custom error messages in Indonesian
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar. Silakan gunakan username lain.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // 1. Create User
            Log::info('Step 1: Creating User');
            try {
                $user = User::create([
                    'name' => $request->nama,
                    'email' => $request->username, // Username disimpan di kolom email
                    'password' => Hash::make($request->password),
                    'view_password' => $request->password,
                ]);

                // Berikan role 'pendaftar' agar bisa login sebagai pendaftar (belum jadi murid)
                $user->assignRole('pendaftar');
                Log::info('Step 1: User created', ['id' => $user->id, 'username' => $request->username]);
            } catch (\Exception $e) {
                throw new \Exception('Gagal membuat akun user. Error: ' . $e->getMessage());
            }

            // 2. Create Siswa Data
            Log::info('Step 2: Creating SiswaData');
            try {
                // Gunakan nama_lengkap jika ada, jika tidak gunakan nama
                $namaLengkap = $request->filled('nama_lengkap') ? $request->nama_lengkap : $request->nama;

                $siswaData = SiswaData::create([
                    'nama' => $request->nama, // Field wajib dari form
                    'nama_lengkap' => $namaLengkap,
                    'user_id' => $user->id, // Tautkan ke akun login
                    'email' => $request->email, // Email opsional (berbeda dari username),
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'kota_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'no_telepon' => $request->no_telepon,
                    'agama' => $request->agama,

                    // Extended Fields
                    'golongan_darah' => $request->golongan_darah,
                    'kewarganegaraan' => $request->input('kewarganegaraan', 'WNI'),
                    'no_ktp' => $request->no_ktp,
                    'no_kk' => $request->no_kk,
                    'anak_ke' => $request->anak_ke,
                    'jumlah_saudara' => $request->jumlah_saudara,

                    // Address Details
                    'nomor_rumah' => $request->nomor_rumah,
                    'dusun' => $request->dusun,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'desa' => $request->desa,
                    'kecamatan' => $request->kecamatan,
                    'kabupaten' => $request->kabupaten,
                    'provinsi' => $request->provinsi,
                    'kode_pos' => $request->kode_pos,
                    'tempat_domisili' => $request->tempat_domisili,
                    'jenis_domisili' => $request->jenis_domisili,

                    // School History Fields
                    'asal_slta' => $request->asal_slta,
                    'status_asal_sekolah' => $request->status_asal_sekolah,
                    'jenis_slta' => $request->jenis_slta,
                    'kejuruan_slta' => $request->kejuruan_slta,
                    'tahun_lulus_slta' => $request->tahun_lulus_slta,
                    'nisn' => $request->nisn,
                    'nomor_seri_ijazah_slta' => $request->nomor_seri_ijazah_slta,
                ]);
                Log::info('Step 2: SiswaData created', ['id' => $siswaData->id]);
            } catch (\Exception $e) {
                throw new \Exception('Gagal menyimpan data siswa ke database. Error: ' . $e->getMessage());
            }

            // 3. Create Siswa Data Orang Tua
            Log::info('Step 3: Creating SiswaDataOrangTua');
            try {
                SiswaDataOrangTua::create([
                    'id_siswa_data' => $siswaData->id,

                    // Ayah
                    'Nama_Ayah' => $request->Nama_Ayah,
                    'Tempat_Lhr_Ayah' => $request->Tempat_Lhr_Ayah,
                    'Tgl_Lhr_Ayah' => $request->Tgl_Lhr_Ayah,
                    'Bln_Lhr_Ayah' => $request->Bln_Lhr_Ayah,
                    'Thn_Lhr_ayah' => $request->Thn_Lhr_ayah,
                    'Agama_Ayah' => $request->Agama_Ayah,
                    'Gol_Darah_Ayah' => $request->Gol_Darah_Ayah,
                    'Pendidikan_Terakhir_Ayah' => $request->Pendidikan_Terakhir_Ayah,
                    'Pekerjaan_Ayah' => $request->Pekerjaan_Ayah,
                    'Penghasilan_Ayah' => $request->Penghasilan_Ayah,
                    'Kebutuhan_Khusus_Ayah' => $request->Kebutuhan_Khusus_Ayah,
                    'Nomor_KTP_Ayah' => $request->Nomor_KTP_Ayah,
                    'Alamat_Ayah' => $request->Alamat_Ayah,
                    'No_HP_ayah' => $request->No_HP_ayah,
                    'Kewarganegaraan_Ayah' => 'WNI',

                    // Ibu
                    'Nama_Ibu' => $request->Nama_Ibu,
                    'Tempat_Lhr_Ibu' => $request->Tempat_Lhr_Ibu,
                    'Tgl_Lhr_Ibu' => $request->Tgl_Lhr_Ibu,
                    'Bln_Lhr_Ibu' => $request->Bln_Lhr_Ibu,
                    'Thn_Lhr_Ibu' => $request->Thn_Lhr_Ibu,
                    'Agama_Ibu' => $request->Agama_Ibu,
                    'Gol_Darah_Ibu' => $request->Gol_Darah_Ibu,
                    'Pendidikan_Terakhir_Ibu' => $request->Pendidikan_Terakhir_Ibu,
                    'Pekerjaan_Ibu' => $request->Pekerjaan_Ibu,
                    'Penghasilan_Ibu' => $request->Penghasilan_Ibu,
                    'Kebutuhan_Khusus_Ibu' => $request->Kebutuhan_Khusus_Ibu,
                    'Nomor_KTP_Ibu' => $request->Nomor_KTP_Ibu,
                    'Alamat_Ibu' => $request->Alamat_Ibu,
                    'No_HP_ibu' => $request->No_HP_ibu,
                    'Kewarganegaraan_Ibu' => 'WNI',
                ]);
                Log::info('Step 3: SiswaDataOrangTua created');
            } catch (\Exception $e) {
                throw new \Exception('Gagal menyimpan data orang tua ke database. Error: ' . $e->getMessage());
            }

            // 4. Create Siswa Data Pendaftar
            Log::info('Step 4: Creating SiswaDataPendaftar');
            try {
                $pendaftar = SiswaDataPendaftar::create([
                    'id_siswa_data' => $siswaData->id,


                    // Registration Details
                    'Nama_Lengkap' => $namaLengkap,
                    'Tgl_Daftar' => now()->toDateString(),
                    'id_tahun_akademik' => \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id,
                    'ro_program_sekolah' => $request->ro_program_sekolah,
                    'Kelas_Program_Kuliah' => $request->Kelas_Program_Kuliah,
                    'id_jurusan' => $request->id_jurusan,
                    'Jalur_PMB' => $request->Jalur_PMB, // Must be ID
                    'Jenis_Pembiayaan' => $request->Jenis_Pembiayaan,

                    // Biaya Pendaftaran otomatis dari Jalur PMB
                    'Biaya_Pendaftaran' => (function () use ($request) {
                        $ref = \App\Models\ReferenceOption::find($request->Jalur_PMB);
                        if ($ref && preg_match('/Rp\.\s*([\d.]+)/', $ref->deskripsi, $matches)) {
                            return (int) str_replace('.', '', $matches[1]);
                        }
                        return 0;
                    })(),

                    // Transfer Data
                    'NIMKO_Asal' => $request->NIMKO_Asal,
                    'Prodi_Asal' => $request->Prodi_Asal,
                    'PT_Asal' => $request->PT_Asal,
                    'Jml_SKS_Asal' => $request->Jml_SKS_Asal,
                    'IPK_Asal' => $request->IPK_Asal,
                    'Semester_Asal' => $request->Semester_Asal,

                    // Referal
                    'id_referal_code' => $request->id_referal_code,

                    // Documents & Photos will be updated after creation

                    'status_valid' => '0',
                ]);
                Log::info('Step 4: SiswaDataPendaftar created', ['id' => $pendaftar->id]);

                // 4.5 Process Multiple File Uploads
                Log::info('Step 4.5: Processing File Uploads');
                $fileFields = [
                    'Legalisir_Ijazah',
                    'Legalisir_SKHU',
                    'Copy_KTP',
                    'Foto_BW_3x3',
                    'Foto_BW_3x4',
                    'Foto_Warna_5x6',
                    'File_Foto_Berwarna'
                ];

                $uploadedPaths = [];
                foreach ($fileFields as $field) {
                    if ($request->hasFile($field)) {
                        $paths = [];
                        $files = is_array($request->file($field)) ? $request->file($field) : [$request->file($field)];

                        // We use the helper carefully, passing null for $get (since it's not a Filament context) and $pendaftar for record
                        $dirPath = \App\Helpers\UploadPathHelper::uploadPendaftarPath(null, $pendaftar, $field);

                        foreach ($files as $file) {
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs($dirPath, $filename, 'public');
                            $paths[] = $path;
                        }

                        if (!empty($paths)) {
                            $uploadedPaths[$field] = $paths;
                        }
                    }
                }

                if (!empty($uploadedPaths)) {
                    $pendaftar->update($uploadedPaths);
                    Log::info('Files uploaded and paths updated for Pendaftar', ['uploaded_fields' => array_keys($uploadedPaths)]);
                }

                // 5. Otomatis buat Program Seleksi (Tahap 1 & 2)
                \App\Models\SiswaSeleksiPendaftar::create([
                    'id_siswa_data_pendaftar' => $pendaftar->id,
                    'nama_seleksi' => 'Verifikasi Administrasi & Tes Tulis',
                    'tanggal_seleksi' => now()->addDays(2),
                    'deskripsi_seleksi' => 'Silakan datang ke kampus sesuai jadwal untuk mengikuti tes tulis dan membawa dokumen fisik.',
                    'status_seleksi' => 'B',
                ]);

                \App\Models\SiswaSeleksiPendaftar::create([
                    'id_siswa_data_pendaftar' => $pendaftar->id,
                    'nama_seleksi' => 'Wawancara & Portofolio',
                    'tanggal_seleksi' => now()->addDays(4),
                    'deskripsi_seleksi' => 'Wawancara dilakukan secara online/offline. Silakan siapkan berkas jurnal jika diminta.',
                    'status_seleksi' => 'B',
                ]);
            } catch (\Exception $e) {
                throw new \Exception('Gagal menyimpan data pendaftaran ke database. Error: ' . $e->getMessage());
            }

            Log::info('All steps completed, committing transaction');
            DB::commit();
            Log::info('Transaction committed successfully');

            return redirect()->route('pendaftaran.index')
                ->with('success', 'Pendaftaran berhasil! Akun Anda telah dibuat dengan username: ' . $request->username . '. Silakan login untuk melanjutkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Pendaftaran failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_email' => $request->email,
                'user_name' => $request->nama_lengkap,
            ]);

            // Determine user-friendly error message
            $errorMessage = $e->getMessage();

            // Check if it's a database connection error
            if (strpos($errorMessage, 'SQLSTATE') !== false) {
                $errorMessage = 'Terjadi kesalahan koneksi database. Silakan hubungi administrator. Detail: ' . $errorMessage;
            }

            // Check if it's a foreign key constraint error
            if (strpos($errorMessage, 'foreign key constraint') !== false || strpos($errorMessage, 'Integrity constraint violation') !== false) {
                $errorMessage = 'Terjadi kesalahan relasi data. Pastikan semua data referensi valid. Detail: ' . $errorMessage;
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }
}
