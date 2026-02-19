<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAkademik;
use App\Models\ReferenceOption;
use App\Models\JenjangPendidikan;
use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Models\MataPelajaranMaster;
use App\Models\Kurikulum;
use App\Models\MataPelajaranKurikulum;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\SiswaData;
use App\Models\SiswaDataOrangTua;
use App\Models\SiswaDataPendaftar;
use App\Models\RiwayatPendidikan;
use App\Models\AkademikKrs;
use App\Models\AbsensiSiswa;
use App\Models\SiswaDataLJK;
use App\Models\DosenData;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // --- 0. Reference Options Setup ---
        $this->ensureReferenceOption('program_kelas', 'A', 'Reguler Pagi');
        $this->ensureReferenceOption('status_siswa', 'A', 'Aktif');
        $this->ensureReferenceOption('status_siswa', 'P', 'Pendaftar');
        $this->ensureReferenceOption('agama', 'ISL', 'Islam');
        $this->ensureReferenceOption('jenis_kelamin', 'L', 'Laki-Laki');

        // Reference options for dosen
        $this->ensureReferenceOption('status_dosen', 'A', 'Aktif');
        $this->ensureReferenceOption('status_dosen', 'N', 'Non-Aktif');
        $this->ensureReferenceOption('jenis_ptk', 'D', 'Dosen Tetap');
        $this->ensureReferenceOption('jenis_ptk', 'DT', 'Dosen Tidak Tetap');
        $this->ensureReferenceOption('jabatan_fungsional', 'AA', 'Asisten Ahli');
        $this->ensureReferenceOption('jabatan_fungsional', 'L', 'Lektor');
        $this->ensureReferenceOption('jabatan_fungsional', 'LK', 'Lektor Kepala');
        $this->ensureReferenceOption('jabatan_fungsional', 'GB', 'Guru Besar');
        $this->ensureReferenceOption('pendidikan_tertinggi', 'S2', 'Magister');
        $this->ensureReferenceOption('pendidikan_tertinggi', 'S3', 'Doktor');

        // Reference options for ruang kelas
        $this->ensureReferenceOption('ruang_kelas', 'RK101', 'Ruang Kelas 101');
        $this->ensureReferenceOption('ruang_kelas', 'RK102', 'Ruang Kelas 102');
        $this->ensureReferenceOption('ruang_kelas', 'RK103', 'Ruang Kelas 103');
        $this->ensureReferenceOption('ruang_kelas', 'LAB1', 'Laboratorium 1');
        $this->ensureReferenceOption('ruang_kelas', 'LAB2', 'Laboratorium 2');
        $this->ensureReferenceOption('ruang_kelas', 'AULA', 'Aula');

        $roProgramKelas = ReferenceOption::where('nama_grup', 'program_kelas')->where('kode', 'A')->first()->id;
        $roStatusSiswaAktif = ReferenceOption::where('nama_grup', 'status_siswa')->where('kode', 'A')->first()->id;
        $roStatusDosenAktif = ReferenceOption::where('nama_grup', 'status_dosen')->where('kode', 'A')->first()->id;
        $roJenisPtkTetap = ReferenceOption::where('nama_grup', 'jenis_ptk')->where('kode', 'D')->first()->id;
        $roJabatanFungsional = ReferenceOption::where('nama_grup', 'jabatan_fungsional')->where('kode', 'L')->first()->id;
        $roPendidikanTertinggi = ReferenceOption::where('nama_grup', 'pendidikan_tertinggi')->where('kode', 'S2')->first()->id;
        $roRuangKelas = ReferenceOption::where('nama_grup', 'ruang_kelas')->inRandomOrder()->first()->id;
        // --- End Reference Options ---

        // --- 1. Tahun Akademik ---
        $taGanjil = TahunAkademik::create(['nama' => '2025/2026', 'periode' => 'Ganjil', 'status' => 'Y']);
        $taGenap = TahunAkademik::create(['nama' => '2025/2026', 'periode' => 'Genap', 'status' => 'N']);
        // --- End Tahun Akademik ---

        // ================= KAMPUS (S1) =================
        $jenjangS1 = JenjangPendidikan::create(['nama' => 'S1', 'deskripsi' => 'Sarjana Strata 1', 'type' => 'kampus']);

        // 2 Fakultas
        for ($i = 1; $i <= 2; $i++) {
            $fakultas = Fakultas::create(['nama' => "Fakultas Teknik $i"]);

            // 2 Jurusan per Fakultas
            for ($j = 1; $j <= 2; $j++) {
                $jurusan = Jurusan::create([
                    'nama' => "Teknik Informatika $j - FT $i",
                    'id_fakultas' => $fakultas->id,
                    'id_jenjang_pendidikan' => $jenjangS1->id
                ]);

                // --- Create 5 Dosen for each Jurusan ---
                $dosenList = [];
                for ($d = 1; $d <= 5; $d++) {
                    $jenisKelamin = $faker->randomElement(['L', 'P']);
                    $gelarDepan = $jenisKelamin == 'L' ? 'Dr.' : 'Dr.';
                    $gelarBelakang = $faker->randomElement(['M.Kom', 'M.T', 'Ph.D', 'M.Sc']);

                    $dosen = DosenData::create([
                        'nama' => $faker->firstName,
                        'id_jurusan' => $jurusan->id,
                    ]);

                    $dosenList[] = $dosen;
                }
                // --- End Create Dosen ---

                // Create 7 Mata Pelajaran Master for this Jurusan
                $masterMatkuls = [];
                for ($m = 1; $m <= 7; $m++) {
                    $masterMatkuls[] = MataPelajaranMaster::create([
                        'nama' => "Algoritma & Struktur Data $m",
                        'kode_feeder' => "FT$i-J$j-MK$m",
                        'id_jurusan' => $jurusan->id,
                        'bobot' => 3
                    ]);
                }

                // Create 2 Kurikulum with different subject sets
                // Kurikulum 1: Subjects 1-5
                $kurikulum1 = Kurikulum::create([
                    'nama' => "Kurikulum 2023 - A",
                    'id_jurusan' => $jurusan->id,
                    'id_tahun_akademik' => $taGanjil->id,
                    'status_aktif' => 'Y'
                ]);

                for ($mk = 0; $mk < 5; $mk++) {
                    MataPelajaranKurikulum::create([
                        'id_kurikulum' => $kurikulum1->id,
                        'id_mata_pelajaran_master' => $masterMatkuls[$mk]->id,
                        'semester' => 1
                    ]);
                }

                // Kurikulum 2: Subjects 3-7 (overlap to simulate different set)
                $kurikulum2 = Kurikulum::create([
                    'nama' => "Kurikulum 2023 - B",
                    'id_jurusan' => $jurusan->id,
                    'id_tahun_akademik' => $taGanjil->id,
                    'status_aktif' => 'N'
                ]);

                for ($mk = 2; $mk < 7; $mk++) {
                    MataPelajaranKurikulum::create([
                        'id_kurikulum' => $kurikulum2->id,
                        'id_mata_pelajaran_master' => $masterMatkuls[$mk]->id,
                        'semester' => 2
                    ]);
                }

                // Create 2 Kelas for this Jurusan (using Kurikulum 1)
                for ($c = 1; $c <= 2; $c++) {
                    $kelas = Kelas::create([
                        'ro_program_kelas' => $roProgramKelas,
                        'semester' => 1,
                        'id_tahun_akademik' => $taGanjil->id,
                        'id_jurusan' => $jurusan->id,
                        'status_aktif' => 'Y'
                    ]);

                    // Get 5 subjects from Kurikulum 1 for this class
                    $mpKurikulums = MataPelajaranKurikulum::where('id_kurikulum', $kurikulum1->id)->get();
                    $mpKelasList = [];

                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    $times = ['07:30-09:10', '09:30-11:10', '13:00-14:40', '15:00-16:40'];

                    foreach ($mpKurikulums as $index => $mpk) {
                        // Assign dosen to this mata pelajaran kelas (rotate dosen)
                        $dosenPengampu = $dosenList[$index % count($dosenList)];

                        // Randomize UTS and UAS dates
                        $utsDate = Carbon::now()->addMonths(2)->startOfMonth()->addDays(rand(0, 20));
                        $uasDate = Carbon::now()->addMonths(5)->startOfMonth()->addDays(rand(0, 20));

                        $mpKelas = MataPelajaranKelas::create([
                            'id_mata_pelajaran_kurikulum' => $mpk->id,
                            'id_kelas' => $kelas->id,
                            'id_dosen_data' => $dosenPengampu->id, // Dosen langsung di sini
                            'uts' => $utsDate,
                            'uas' => $uasDate,
                            'ro_ruang_kelas' => $roRuangKelas,
                            'ro_pelaksanaan_kelas' => null, // Optional
                            'id_pengawas' => null, // Optional, bisa diisi dengan dosen lain
                            'jumlah' => 5, // Capacity
                            'hari' => $days[array_rand($days)],
                            'tanggal' => Carbon::now()->addDays(rand(1, 30)),
                            'soal_uts' => $faker->optional()->paragraph,
                            'soal_uas' => $faker->optional()->paragraph,
                            'jam' => $times[array_rand($times)],
                            'status_uts' => 'Y',
                            'status_uas' => 'Y',
                            'ruang_uts' => 'Ruang UTS ' . rand(101, 105),
                            'ruang_uas' => 'Ruang UAS ' . rand(106, 110),
                            'link_kelas' => $faker->optional()->url,
                            'passcode' => $faker->optional()->bothify('??####'),
                            'ctt_soal_uts' => $faker->optional()->sentence,
                            'ctt_soal_uas' => $faker->optional()->sentence,
                        ]);
                        $mpKelasList[] = $mpKelas;
                    }

                    // 5 Mahasiswa Aktif per Kelas
                    for ($s = 1; $s <= 5; $s++) {
                        // Create Siswa (with pendaftar data for kampus)
                        $siswa = $this->createSiswa($faker, $jurusan->id, true);

                        // Create Riwayat Pendidikan
                        $riwayat = RiwayatPendidikan::create([
                            'id_siswa_data' => $siswa->id,
                            'id_jurusan' => $jurusan->id,
                            'ro_status_siswa' => $roStatusSiswaAktif,
                            'angkatan' => 2025,
                            'status' => 'Y'
                        ]);

                        // Create KRS
                        $krs = AkademikKrs::create([
                            'id_riwayat_pendidikan' => $riwayat->id,
                            'id_kelas' => $kelas->id,
                            'jumlah_sks' => 15,
                            'status_aktif' => 'Y',
                            'status_bayar' => 'Y'
                        ]);

                        // Create Absensi (2x) and LJK for each subject
                        foreach ($mpKelasList as $mpKelas) {
                            // 2 Absensi records
                            AbsensiSiswa::create([
                                'id_krs' => $krs->id,
                                'id_mata_pelajaran_kelas' => $mpKelas->id,
                                'status' => 'Hadir',
                                'waktu_absen' => Carbon::now()->subDays(rand(1, 5))
                            ]);
                            AbsensiSiswa::create([
                                'id_krs' => $krs->id,
                                'id_mata_pelajaran_kelas' => $mpKelas->id,
                                'status' => 'Hadir',
                                'waktu_absen' => Carbon::now()->subDays(rand(6, 10))
                            ]);

                            // LJK record
                            SiswaDataLJK::create([
                                'id_akademik_krs' => $krs->id,
                                'id_mata_pelajaran_kelas' => $mpKelas->id,
                                'nilai' => rand(70, 90),
                                'Nilai_UTS' => rand(60, 85),
                                'Nilai_UAS' => rand(70, 95)
                            ]);
                        }
                    }
                }
            }
        }

        // Additional Kampus Students
        $jurusanSample = Jurusan::whereHas('jenjangPendidikan', function ($q) {
            $q->where('type', 'kampus');
        })->first();

        // 3 Mahasiswa Aktif (with Riwayat) but not in any class
        for ($i = 0; $i < 3; $i++) {
            $siswa = $this->createSiswa($faker, $jurusanSample->id, true);
            RiwayatPendidikan::create([
                'id_siswa_data' => $siswa->id,
                'id_jurusan' => $jurusanSample->id,
                'ro_status_siswa' => $roStatusSiswaAktif,
                'angkatan' => 2025,
                'status' => 'Y'
            ]);
            // No KRS created, so they are active but not in class
        }

        // 3 Mahasiswa Pendaftar (SiswaDataPendaftar only, no Riwayat)
        for ($i = 0; $i < 3; $i++) {
            $siswa = $this->createSiswa($faker, $jurusanSample->id, true);
            // SiswaDataPendaftar already created by createSiswa(true)
            // But we need to ensure Status_Pendaftaran is 'P' (Pendaftar)
            $pendaftar = SiswaDataPendaftar::where('id_siswa_data', $siswa->id)->first();
            if ($pendaftar) {
                $pendaftar->update(['Status_Pendaftaran' => 'B']); // Pendaftar
            }
            // No RiwayatPendidikan created
        }

        // ================= SEKOLAH (MA, SMP) =================
        $sekolahJenjangs = ['MA', 'SMP'];

        foreach ($sekolahJenjangs as $jenisSekolah) {
            $jenjangSekolah = JenjangPendidikan::create([
                'nama' => $jenisSekolah,
                'deskripsi' => $jenisSekolah == 'MA' ? 'Madrasah Aliyah' : 'Sekolah Menengah Pertama',
                'type' => 'sekolah'
            ]);

            // Create a dummy fakultas for sekolah (required field)
            $fakultasUmum = Fakultas::firstOrCreate(['nama' => 'Fakultas Umum Sekolah']);

            // Jurusan IPA and IPS for each sekolah type
            $daftarJurusan = ['IPA', 'IPS'];

            foreach ($daftarJurusan as $jurusanName) {
                $jurusan = Jurusan::create([
                    'nama' => $jurusanName . ' ' . $jenisSekolah,
                    'id_fakultas' => $fakultasUmum->id,
                    'id_jenjang_pendidikan' => $jenjangSekolah->id
                ]);

                // --- Create 5 Guru/Dosen for each Jurusan Sekolah ---
                $guruList = [];
                for ($d = 1; $d <= 5; $d++) {
                    $jenisKelamin = $faker->randomElement(['L', 'P']);
                    $gelarBelakang = $faker->randomElement(['S.Pd', 'M.Pd', 'S.Si', 'M.Si']);

                    $guru = DosenData::create([
                        'nama' => $faker->firstName,
                        'id_jurusan' => $jurusan->id,
                    ]);

                    $guruList[] = $guru;
                }
                // --- End Create Guru ---

                // 7 Mata Pelajaran Master for this jurusan
                $masterMatpels = [];
                for ($m = 1; $m <= 7; $m++) {
                    $masterMatpels[] = MataPelajaranMaster::create([
                        'nama' => $jurusanName . ' - Pelajaran ' . $m,
                        'kode_feeder' => $jenisSekolah . '-' . $jurusanName . '-MP' . $m,
                        'id_jurusan' => $jurusan->id,
                        'bobot' => 1
                    ]);
                }

                // 2 Kurikulum with different subjects
                // Kurikulum 1: Subjects 1-5
                $kurikulum1 = Kurikulum::create([
                    'nama' => 'Kurikulum Merdeka - Fase E',
                    'id_jurusan' => $jurusan->id,
                    'id_tahun_akademik' => $taGanjil->id,
                    'status_aktif' => 'Y'
                ]);

                for ($mk = 0; $mk < 5; $mk++) {
                    MataPelajaranKurikulum::create([
                        'id_kurikulum' => $kurikulum1->id,
                        'id_mata_pelajaran_master' => $masterMatpels[$mk]->id,
                        'semester' => 1
                    ]);
                }

                // Kurikulum 2: Subjects 3-7
                $kurikulum2 = Kurikulum::create([
                    'nama' => 'Kurikulum Merdeka - Fase F',
                    'id_jurusan' => $jurusan->id,
                    'id_tahun_akademik' => $taGanjil->id,
                    'status_aktif' => 'N'
                ]);

                for ($mk = 2; $mk < 7; $mk++) {
                    MataPelajaranKurikulum::create([
                        'id_kurikulum' => $kurikulum2->id,
                        'id_mata_pelajaran_master' => $masterMatpels[$mk]->id,
                        'semester' => 2
                    ]);
                }

                // 2 Kelas for this jurusan
                for ($c = 1; $c <= 2; $c++) {
                    $kelas = Kelas::create([
                        'ro_program_kelas' => $roProgramKelas,
                        'semester' => 1,
                        'id_tahun_akademik' => $taGanjil->id,
                        'id_jurusan' => $jurusan->id,
                        'status_aktif' => 'Y'
                    ]);

                    // Get 5 subjects from Kurikulum 1 for this class
                    $mpKurikulums = MataPelajaranKurikulum::where('id_kurikulum', $kurikulum1->id)->get();
                    $mpKelasList = [];

                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    $times = ['07:30-09:10', '09:30-11:10', '13:00-14:40', '15:00-16:40'];

                    foreach ($mpKurikulums as $index => $mpk) {
                        // Assign guru to this mata pelajaran kelas (rotate guru)
                        $guruPengampu = $guruList[$index % count($guruList)];

                        // Randomize UTS and UAS dates
                        $utsDate = Carbon::now()->addMonths(2)->startOfMonth()->addDays(rand(0, 20));
                        $uasDate = Carbon::now()->addMonths(5)->startOfMonth()->addDays(rand(0, 20));

                        $mpKelas = MataPelajaranKelas::create([
                            'id_mata_pelajaran_kurikulum' => $mpk->id,
                            'id_kelas' => $kelas->id,
                            'id_dosen_data' => $guruPengampu->id, // Guru langsung di sini
                            'uts' => $utsDate,
                            'uas' => $uasDate,
                            'ro_ruang_kelas' => $roRuangKelas,
                            'ro_pelaksanaan_kelas' => null,
                            'id_pengawas' => null,
                            'jumlah' => 5,
                            'hari' => $days[array_rand($days)],
                            'tanggal' => Carbon::now()->addDays(rand(1, 30)),
                            'soal_uts' => $faker->optional()->paragraph,
                            'soal_uas' => $faker->optional()->paragraph,
                            'jam' => $times[array_rand($times)],
                            'status_uts' => 'Y',
                            'status_uas' => 'Y',
                            'ruang_uts' => 'Ruang UTS ' . rand(101, 105),
                            'ruang_uas' => 'Ruang UAS ' . rand(106, 110),
                            'link_kelas' => $faker->optional()->url,
                            'passcode' => $faker->optional()->bothify('??####'),
                            'ctt_soal_uts' => $faker->optional()->sentence,
                            'ctt_soal_uas' => $faker->optional()->sentence,
                        ]);
                        $mpKelasList[] = $mpKelas;
                    }

                    // 5 Siswa Aktif per Kelas
                    for ($s = 1; $s <= 5; $s++) {
                        // Create Siswa (with pendaftar = false for sekolah)
                        $siswa = $this->createSiswa($faker, $jurusan->id, false);

                        // Riwayat Pendidikan
                        $riwayat = RiwayatPendidikan::create([
                            'id_siswa_data' => $siswa->id,
                            'id_jurusan' => $jurusan->id,
                            'ro_status_siswa' => $roStatusSiswaAktif,
                            'angkatan' => 2025,
                            'status' => 'Y'
                        ]);

                        // KRS (AkademikKrs for sekolah)
                        $krs = AkademikKrs::create([
                            'id_riwayat_pendidikan' => $riwayat->id,
                            'id_kelas' => $kelas->id,
                            'status_aktif' => 'Y'
                        ]);

                        // Absensi (2x) and LJK for each subject
                        foreach ($mpKelasList as $mpKelas) {
                            // 2 Absensi records
                            AbsensiSiswa::create([
                                'id_krs' => $krs->id,
                                'id_mata_pelajaran_kelas' => $mpKelas->id,
                                'status' => 'Hadir',
                                'waktu_absen' => Carbon::now()->subDays(rand(1, 5))
                            ]);
                            AbsensiSiswa::create([
                                'id_krs' => $krs->id,
                                'id_mata_pelajaran_kelas' => $mpKelas->id,
                                'status' => 'Hadir',
                                'waktu_absen' => Carbon::now()->subDays(rand(6, 10))
                            ]);

                            // LJK record
                            SiswaDataLJK::create([
                                'id_akademik_krs' => $krs->id,
                                'id_mata_pelajaran_kelas' => $mpKelas->id,
                                'nilai' => rand(70, 90),
                                'Nilai_UTS' => rand(60, 85),
                                'Nilai_UAS' => rand(70, 95)
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('Dummy data dengan dosen berhasil dibuat!');
    }

    /**
     * Helper to ensure reference option exists
     */
    private function ensureReferenceOption($grup, $kode, $nilai)
    {
        if (!ReferenceOption::where('nama_grup', $grup)->where('kode', $kode)->exists()) {
            ReferenceOption::create([
                'nama_grup' => $grup,
                'kode' => $kode,
                'nilai' => $nilai,
                'status' => 'Y'
            ]);
        }
    }

    /**
     * Helper to create Siswa with related data
     */
    private function createSiswa($faker, $jurusanId, $withPendaftar = false)
    {
        $siswa = SiswaData::create([
            'nama' => $faker->firstName,
            'nama_lengkap' => $faker->name,
            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
            'email' => $faker->unique()->safeEmail,
            'user_id' => null
        ]);

        // Create Orang Tua data
        SiswaDataOrangTua::create([
            'id_siswa_data' => $siswa->id,
            'nama_ayah' => $faker->name('male'),
            'nama_ibu' => $faker->name('female')
        ]);

        // Create Pendaftar data if requested (for kampus)
        if ($withPendaftar) {
            SiswaDataPendaftar::create([
                'id_siswa_data' => $siswa->id,
                'id_jurusan' => $jurusanId,
                'Status_Pendaftaran' => 'Y',
                'Status_Kelulusan' => 'Y'
            ]);
        }

        return $siswa;
    }
}
