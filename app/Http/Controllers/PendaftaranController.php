<?php

namespace App\Http\Controllers;

use App\Models\SiswaData;
use App\Models\SiswaDataOrangTua;
use App\Models\SiswaDataPendaftar;
use App\Models\User;
use App\Models\JenjangPendidikan;
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
    public function index()
    {
        $jenjangs = JenjangPendidikan::all();
        $jurusans = \App\Models\Jurusan::all();
        $jalurPmbs = \App\Models\ReferenceOption::where('nama_grup', 'jalur_pmb')->where('status', 1)->get();
        // Ambil reference option untuk program sekolah
        $programSekolahs = \App\Models\ReferenceOption::where('nama_grup', 'program_sekolah')->where('status', 1)->get();
        return view('pendaftaran.index', compact('jenjangs', 'jurusans', 'jalurPmbs', 'programSekolahs'));
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
            'id_jurusan' => ['nullable', 'exists:jurusan,id'],
            'ro_program_sekolah' => ['nullable', 'exists:reference_option,id'],
            'Jalur_PMB' => ['nullable', 'exists:reference_option,id'], // ID Reference Option
            'Jenis_Pembiayaan' => ['nullable', 'string', 'max:255'],
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
            'Legalisir_Ijazah' => ['nullable', 'string'],
            'Legalisir_SKHU' => ['nullable', 'string'],
            'Copy_KTP' => ['nullable', 'string'],
            // Photos
            'File_Foto_Berwarna' => ['nullable', 'string'],
            'Foto_BW_3x3' => ['nullable', 'string'],
            'Foto_BW_3x4' => ['nullable', 'string'],
            'Foto_Warna_5x6' => ['nullable', 'string'],
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

            // Jenjang Selection
            'id_jenjang_pendidikan' => ['required', 'exists:jenjang_pendidikan,id'],
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
                ]);
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
                    'id_jenjang_pendidikan' => $request->id_jenjang_pendidikan,

                    // Registration Details
                    'Nama_Lengkap' => $namaLengkap,
                    'Tgl_Daftar' => now()->toDateString(),
                    'Tahun_Masuk' => now()->month <= 7
                        ? now()->year . 'genap'
                        : now()->year . 'ganjil',
                    'ro_program_sekolah' => $request->ro_program_sekolah,
                    'Kelas_Program_Kuliah' => $request->Kelas_Program_Kuliah,
                    'id_jurusan' => $request->id_jurusan,
                    'Jalur_PMB' => $request->Jalur_PMB, // Must be ID
                    'Jenis_Pembiayaan' => $request->Jenis_Pembiayaan,

                    // Transfer Data
                    'NIMKO_Asal' => $request->NIMKO_Asal,
                    'Prodi_Asal' => $request->Prodi_Asal,
                    'PT_Asal' => $request->PT_Asal,
                    'Jml_SKS_Asal' => $request->Jml_SKS_Asal,
                    'IPK_Asal' => $request->IPK_Asal,
                    'Semester_Asal' => $request->Semester_Asal,

                    // Documents & Photos
                    'Legalisir_Ijazah' => $request->Legalisir_Ijazah,
                    'Legalisir_SKHU' => $request->Legalisir_SKHU,
                    'Copy_KTP' => $request->Copy_KTP,
                    'File_Foto_Berwarna' => $request->File_Foto_Berwarna,

                    'status_valid' => '0',
                ]);
                Log::info('Step 4: SiswaDataPendaftar created', ['id' => $pendaftar->id]);
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
