<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenjangPendidikan;
use App\Models\Fakultas;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use App\Models\RefOption\ProgramKelas;
use App\Models\RefOption\StatusSiswa;

// AKADEMIK
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\MataPelajaranMaster;
use App\Models\MataPelajaranKurikulum;

// SDM & FASILITAS
use App\Models\DosenData;
use App\Models\RefOption\RuangKelas;

// MAHASISWA
use App\Models\SiswaData;
use App\Models\RiwayatPendidikan;
use App\Models\AkademikKrs;
use App\Models\SiswaDataPendaftar;
use App\Models\SiswaDataOrangTua;

// PROSES BELAJAR
use App\Models\MataPelajaranKelas;
use App\Models\AbsensiSiswa;

// EVALUASI
use App\Models\SiswaDataLjk;
use Illuminate\Support\Facades\DB;
use App\Models\ReferenceOption;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🧹 Membersihkan data lama...');
        
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Hapus data dalam urutan yang benar (dari child ke parent)
        try {
            // EVALUASI
            SiswaDataLjk::truncate();
            
            // PROSES BELAJAR
            MataPelajaranKelas::truncate();
            AbsensiSiswa::truncate();
            
            // MAHASISWA
            AkademikKrs::truncate();
            RiwayatPendidikan::truncate();
            SiswaData::truncate();
            SiswaDataPendaftar::truncate();
            SiswaDataOrangTua::truncate();
            
            // AKADEMIK
            Kelas::truncate();
            Kurikulum::truncate();
            MataPelajaranKurikulum::truncate();
            MataPelajaranMaster::truncate();
            
            // SDM & FASILITAS
            DosenData::truncate();
            
            // REFERENSI
            ReferenceOption::truncate();
            Jurusan::truncate();
            Fakultas::truncate();
            TahunAkademik::truncate();
            JenjangPendidikan::truncate();
            
            $this->command->info('✅ Data lama berhasil dihapus!');
            $this->command->info('');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Gagal menghapus data lama: ' . $e->getMessage());
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return;
        }
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('🚀 Mulai seeding data SIAKAD...');
        $this->command->info('');

        // ============================================
        // 0. REFERENCE OPTIONS (HARUS PERTAMA!)
        // ============================================
        $this->command->info('⚙️ Membuat Reference Options...');
        
        $refOptions = [
            // Jenis Mapel
            ['nama_grup' => 'jenis_mapel', 'kode' => 'A', 'nilai' => 'Wajib Program Studi', 'status' => 'Y'],
            ['nama_grup' => 'jenis_mapel', 'kode' => 'B', 'nilai' => 'Pilihan', 'status' => 'Y'],
            ['nama_grup' => 'jenis_mapel', 'kode' => 'C', 'nilai' => 'Peminatan', 'status' => 'Y'],
            ['nama_grup' => 'jenis_mapel', 'kode' => 'W', 'nilai' => 'Wajib Nasional', 'status' => 'Y'],
            ['nama_grup' => 'jenis_mapel', 'kode' => 'S', 'nilai' => 'Tugas Akhir / Skripsi', 'status' => 'Y'],
            
            // Jabatan Fungsional
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '1', 'nilai' => 'Asisten Ahli - 100.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '2', 'nilai' => 'Asisten Ahli - 150.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '3', 'nilai' => 'Lektor - 200.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '4', 'nilai' => 'Lektor - 300.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '5', 'nilai' => 'Lektor Kepala - 400.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '6', 'nilai' => 'Lektor Kepala - 550.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '7', 'nilai' => 'Lektor Kepala - 700.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '8', 'nilai' => 'Profesor - 850.00', 'status' => 'Y'],
            ['nama_grup' => 'jabatan_fungsional', 'kode' => '9', 'nilai' => 'Profesor - 1050.00', 'status' => 'Y'],
            
            // Pangkat Golongan
            ['nama_grup' => 'pangkat', 'kode' => '1', 'nilai' => 'Juru Muda, I/a', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '2', 'nilai' => 'Juru Muda Tk. I, I/b', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '3', 'nilai' => 'Juru, I/c', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '4', 'nilai' => 'Juru Tk. I, I/d', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '5', 'nilai' => 'Pengatur Muda, II/a', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '6', 'nilai' => 'Pengatur Muda Tk. I, II/b', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '7', 'nilai' => 'Pengatur, II/c', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '8', 'nilai' => 'Pengatur Tk. I, II/d', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '9', 'nilai' => 'Penata Muda, III/a', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '10', 'nilai' => 'Penata Muda Tk. I, III/b', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '11', 'nilai' => 'Penata, III/c', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '12', 'nilai' => 'Penata Tk. I, III/d', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '13', 'nilai' => 'Pembina, IV/a', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '14', 'nilai' => 'Pembina Tk. I, IV/b', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '15', 'nilai' => 'Pembina Utama Muda, IV/c', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '16', 'nilai' => 'Pembina Utama Madya, IV/d', 'status' => 'Y'],
            ['nama_grup' => 'pangkat', 'kode' => '17', 'nilai' => 'Pembina Utama, IV/e', 'status' => 'Y'],
            
            // Status Dosen
            ['nama_grup' => 'status_dosen', 'kode' => '1', 'nilai' => 'Dosen Tetap', 'status' => 'Y'],
            ['nama_grup' => 'status_dosen', 'kode' => '2', 'nilai' => 'Dosen Tidak Tetap', 'status' => 'Y'],
            ['nama_grup' => 'status_dosen', 'kode' => '3', 'nilai' => 'Dosen Luar Biasa (DLB)', 'status' => 'Y'],
            ['nama_grup' => 'status_dosen', 'kode' => '4', 'nilai' => 'Pegawai Tetap', 'status' => 'Y'],
            ['nama_grup' => 'status_dosen', 'kode' => '5', 'nilai' => 'Pegawai Tidak Tetap', 'status' => 'Y'],
            
            // Status Siswa
            ['nama_grup' => 'status_siswa', 'kode' => 'A', 'nilai' => 'Aktif', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'C', 'nilai' => 'Cuti', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'D', 'nilai' => 'Drop-Out/Putus Studi', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'G', 'nilai' => 'Sedang Double Degree', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'K', 'nilai' => 'Keluar', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'L', 'nilai' => 'Lulus', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'N', 'nilai' => 'Non-Aktif', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'M', 'nilai' => 'Kampus Merdeka (Pertukaran Pelajar)', 'status' => 'Y'],
            ['nama_grup' => 'status_siswa', 'kode' => 'U', 'nilai' => 'Menunggu Uji Kompetensi', 'status' => 'Y'],
            
            // Agama
            ['nama_grup' => 'agama', 'kode' => '1', 'nilai' => 'Islam', 'status' => 'Y'],
            ['nama_grup' => 'agama', 'kode' => '2', 'nilai' => 'Kristen', 'status' => 'Y'],
            ['nama_grup' => 'agama', 'kode' => '3', 'nilai' => 'Katholik', 'status' => 'Y'],
            ['nama_grup' => 'agama', 'kode' => '4', 'nilai' => 'Hindu', 'status' => 'Y'],
            ['nama_grup' => 'agama', 'kode' => '5', 'nilai' => 'Budha', 'status' => 'Y'],
            ['nama_grup' => 'agama', 'kode' => '6', 'nilai' => 'Konghucu', 'status' => 'Y'],
            ['nama_grup' => 'agama', 'kode' => '99', 'nilai' => 'Lainnya', 'status' => 'Y'],
            
            // Alat Transportasi
            ['nama_grup' => 'alat_transport', 'kode' => '1', 'nilai' => 'Jalan Kaki', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '2', 'nilai' => 'Kendaraan Pribadi', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '3', 'nilai' => 'Angkutan Umum / Bus', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '4', 'nilai' => 'Mobil / Bus Antar Jemput', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '5', 'nilai' => 'Kereta Api', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '6', 'nilai' => 'Ojek', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '12', 'nilai' => 'Sepeda', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '13', 'nilai' => 'Sepeda Motor', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '14', 'nilai' => 'Mobil Pribadi', 'status' => 'Y'],
            ['nama_grup' => 'alat_transport', 'kode' => '99', 'nilai' => 'Lainnya', 'status' => 'Y'],
            
            // Jenis Keluar
            ['nama_grup' => 'jns_keluar', 'kode' => '1', 'nilai' => 'Lulus', 'status' => 'Y'],
            ['nama_grup' => 'jns_keluar', 'kode' => '2', 'nilai' => 'Mutasi', 'status' => 'Y'],
            ['nama_grup' => 'jns_keluar', 'kode' => '3', 'nilai' => 'Dikeluarkan', 'status' => 'Y'],
            ['nama_grup' => 'jns_keluar', 'kode' => '4', 'nilai' => 'Mengundurkan Diri', 'status' => 'Y'],
            ['nama_grup' => 'jns_keluar', 'kode' => '5', 'nilai' => 'Putus Sekolah', 'status' => 'Y'],
            ['nama_grup' => 'jns_keluar', 'kode' => '6', 'nilai' => 'Wafat', 'status' => 'Y'],
            ['nama_grup' => 'jns_keluar', 'kode' => '9', 'nilai' => 'Pensiun', 'status' => 'Y'],
            
            // Jenis Pendaftaran
            ['nama_grup' => 'jns_pendaftaran', 'kode' => '1', 'nilai' => 'Peserta Didik Baru', 'status' => 'Y'],
            ['nama_grup' => 'jns_pendaftaran', 'kode' => '2', 'nilai' => 'Pindahan', 'status' => 'Y'],
            ['nama_grup' => 'jns_pendaftaran', 'kode' => '8', 'nilai' => 'Pindahan Alih Bentuk', 'status' => 'Y'],
            
            // Jenis Tinggal
            ['nama_grup' => 'jns_tinggal', 'kode' => '1', 'nilai' => 'Bersama Orang Tua', 'status' => 'Y'],
            ['nama_grup' => 'jns_tinggal', 'kode' => '2', 'nilai' => 'Wali', 'status' => 'Y'],
            ['nama_grup' => 'jns_tinggal', 'kode' => '3', 'nilai' => 'Kost', 'status' => 'Y'],
            ['nama_grup' => 'jns_tinggal', 'kode' => '4', 'nilai' => 'Asrama / Pesantren', 'status' => 'Y'],
            ['nama_grup' => 'jns_tinggal', 'kode' => '5', 'nilai' => 'Panti Asuhan', 'status' => 'Y'],
            ['nama_grup' => 'jns_tinggal', 'kode' => '6', 'nilai' => 'Rumah Sendiri', 'status' => 'Y'],
            ['nama_grup' => 'jns_tinggal', 'kode' => '99', 'nilai' => 'Lainnya', 'status' => 'Y'],
            
            // Pekerjaan
            ['nama_grup' => 'pekerjaan', 'kode' => '1', 'nilai' => 'Tidak bekerja', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '2', 'nilai' => 'Nelayan', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '3', 'nilai' => 'Petani', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '4', 'nilai' => 'Peternak', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '5', 'nilai' => 'PNS/TNI/Polri', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '6', 'nilai' => 'Karyawan Swasta', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '7', 'nilai' => 'Pedagang Kecil', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '8', 'nilai' => 'Pedagang Besar', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '9', 'nilai' => 'Wiraswasta', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '10', 'nilai' => 'Wirausaha', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '11', 'nilai' => 'Buruh', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '12', 'nilai' => 'Pensiun', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '98', 'nilai' => 'Sudah Meninggal', 'status' => 'Y'],
            ['nama_grup' => 'pekerjaan', 'kode' => '99', 'nilai' => 'Lainnya', 'status' => 'Y'],
            
            // Penghasilan
            ['nama_grup' => 'penghasilan', 'kode' => '0', 'nilai' => 'Rp. 0', 'status' => 'Y'],
            ['nama_grup' => 'penghasilan', 'kode' => '11', 'nilai' => 'Kurang dari Rp. 500.000', 'status' => 'Y'],
            ['nama_grup' => 'penghasilan', 'kode' => '12', 'nilai' => 'Rp. 500.000 - Rp. 999.999', 'status' => 'Y'],
            ['nama_grup' => 'penghasilan', 'kode' => '13', 'nilai' => 'Rp. 1.000.000 - Rp. 1.999.999', 'status' => 'Y'],
            ['nama_grup' => 'penghasilan', 'kode' => '14', 'nilai' => 'Rp. 2.000.000 - Rp. 4.999.999', 'status' => 'Y'],
            ['nama_grup' => 'penghasilan', 'kode' => '15', 'nilai' => 'Rp. 5.000.000 - Rp. 20.000.000', 'status' => 'Y'],
            ['nama_grup' => 'penghasilan', 'kode' => '16', 'nilai' => 'Lebih dari Rp. 20.000.000', 'status' => 'Y'],
            
            // Program Kelas
            ['nama_grup' => 'program_kelas', 'kode' => 'A', 'nilai' => 'Kelas A', 'status' => 'Y'],
            ['nama_grup' => 'program_kelas', 'kode' => 'B', 'nilai' => 'Kelas B', 'status' => 'Y'],
            ['nama_grup' => 'program_kelas', 'kode' => 'C', 'nilai' => 'Kelas C', 'status' => 'Y'],
            ['nama_grup' => 'program_kelas', 'kode' => 'D', 'nilai' => 'Kelas D', 'status' => 'Y'],
            ['nama_grup' => 'program_kelas', 'kode' => 'S2-Reg', 'nilai' => 'S2-Reguler', 'status' => 'Y'],
            ['nama_grup' => 'program_kelas', 'kode' => 'Afiliasi', 'nilai' => 'Afiliasi Kampus', 'status' => 'Y'],
            
            // Hari
            ['nama_grup' => 'hari', 'kode' => '1', 'nilai' => 'Sabtu', 'status' => 'Y'],
            ['nama_grup' => 'hari', 'kode' => '2', 'nilai' => 'Minggu', 'status' => 'Y'],
            ['nama_grup' => 'hari', 'kode' => '3', 'nilai' => 'Senin', 'status' => 'Y'],
            ['nama_grup' => 'hari', 'kode' => '4', 'nilai' => 'Selasa', 'status' => 'Y'],
            ['nama_grup' => 'hari', 'kode' => '5', 'nilai' => 'Rabu', 'status' => 'Y'],
            ['nama_grup' => 'hari', 'kode' => '6', 'nilai' => 'Kamis', 'status' => 'Y'],
            ['nama_grup' => 'hari', 'kode' => '7', 'nilai' => 'Jumat', 'status' => 'Y'],
            
            // Ruang Kelas
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A1', 'nilai' => 'A1', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A2', 'nilai' => 'A2', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A3', 'nilai' => 'A3', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A6', 'nilai' => 'A6', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A7', 'nilai' => 'A7', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A8', 'nilai' => 'A8', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A9', 'nilai' => 'A9', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'A10', 'nilai' => 'A10', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B1', 'nilai' => 'B1', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B2', 'nilai' => 'B2', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B3', 'nilai' => 'B3', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B4', 'nilai' => 'B4', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B5', 'nilai' => 'B5', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B6', 'nilai' => 'B6', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B7', 'nilai' => 'B7', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'B8', 'nilai' => 'B8', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'C1', 'nilai' => 'C1', 'status' => 'Y'],
            ['nama_grup' => 'ruang_kelas', 'kode' => 'C2', 'nilai' => 'C2', 'status' => 'Y'],
            
            // Pelaksanaan Kelas
            ['nama_grup' => 'pelaksanaan_kelas', 'kode' => '1', 'nilai' => 'Pararel Institut', 'status' => 'Y'],
            ['nama_grup' => 'pelaksanaan_kelas', 'kode' => '2', 'nilai' => 'Pararel Lintas Fakultas', 'status' => 'Y'],
            ['nama_grup' => 'pelaksanaan_kelas', 'kode' => '3', 'nilai' => 'Pararel Lintas Prodi Satu Fakultas', 'status' => 'Y'],
            ['nama_grup' => 'pelaksanaan_kelas', 'kode' => '4', 'nilai' => 'Non Pararel', 'status' => 'Y'],
            ['nama_grup' => 'pelaksanaan_kelas', 'kode' => '5', 'nilai' => 'Pararel Antar Semester', 'status' => 'Y'],
            
            // Program Sekolah
            ['nama_grup' => 'program_sekolah', 'kode' => '1', 'nilai' => 'Reguler', 'status' => 'Y'],
            ['nama_grup' => 'program_sekolah', 'kode' => '2', 'nilai' => 'Kelas Karyawan', 'status' => 'Y'],
            ['nama_grup' => 'program_sekolah', 'kode' => '3', 'nilai' => 'Madin', 'status' => 'Y'],
        ];
        
        $refOptionCount = 0;
        foreach ($refOptions as $data) {
            ReferenceOption::create($data);
            $refOptionCount++;
        }
        
        $this->command->info('   ✓ ' . $refOptionCount . ' Reference Options created');

        // ============================================
        // 1. JENJANG PENDIDIKAN
        // ============================================
        $this->command->info('📚 Membuat Jenjang Pendidikan...');
        $jenjangS1 = JenjangPendidikan::create([
            'nama' => 'S1',
            'deskripsi' => 'Sarjana Strata 1',
        ]);
        $this->command->info('   ✓ Jenjang S1 created');
        
        // ============================================
        // 2. TAHUN AKADEMIK (2 tahun)
        // ============================================
        $this->command->info('📅 Membuat Tahun Akademik...');
        $tahunAkademik = [
            TahunAkademik::create([
                'nama' => '2024/2025',
                'periode' => 'Ganjil',
                'status' => 'N',
            ]),
            TahunAkademik::create([
                'nama' => '2024/2025',
                'periode' => 'Genap',
                'status' => 'N',
            ]),
            TahunAkademik::create([
                'nama' => '2025/2026',
                'periode' => 'Ganjil',
                'status' => 'Y',
            ]),
            TahunAkademik::create([
                'nama' => '2025/2026',
                'periode' => 'Genap',
                'status' => 'N',
            ]),
        ];
        $this->command->info('   ✓ ' . count($tahunAkademik) . ' Tahun Akademik created');

        // ============================================
        // 4. FAKULTAS (3 fakultas)
        // ============================================
        $this->command->info('🏛️ Membuat Fakultas...');
        $fakultasList = [
            Fakultas::create(['nama' => 'Fakultas Teknik']),
            Fakultas::create(['nama' => 'Fakultas Ekonomi dan Bisnis']),
            Fakultas::create(['nama' => 'Fakultas Ilmu Sosial dan Politik']),
        ];
        $this->command->info('   ✓ ' . count($fakultasList) . ' Fakultas created');

        // ============================================
        // 5. JURUSAN/PRODI (3 per fakultas = 9 total)
        // ============================================
        $this->command->info('🎓 Membuat Program Studi...');
        $jurusanList = [
            // Fakultas Teknik
            Jurusan::create(['nama' => 'Teknik Informatika', 'id_fakultas' => $fakultasList[0]->id]),
            Jurusan::create(['nama' => 'Sistem Informasi', 'id_fakultas' => $fakultasList[0]->id]),
            Jurusan::create(['nama' => 'Teknik Elektro', 'id_fakultas' => $fakultasList[0]->id]),
            
            // Fakultas Ekonomi
            Jurusan::create(['nama' => 'Manajemen', 'id_fakultas' => $fakultasList[1]->id]),
            Jurusan::create(['nama' => 'Akuntansi', 'id_fakultas' => $fakultasList[1]->id]),
            Jurusan::create(['nama' => 'Ekonomi Pembangunan', 'id_fakultas' => $fakultasList[1]->id]),
            
            // Fakultas Ilmu Sosial dan Politik
            Jurusan::create(['nama' => 'Ilmu Komunikasi', 'id_fakultas' => $fakultasList[2]->id]),
            Jurusan::create(['nama' => 'Administrasi Publik', 'id_fakultas' => $fakultasList[2]->id]),
            Jurusan::create(['nama' => 'Hubungan Internasional', 'id_fakultas' => $fakultasList[2]->id]),
        ];
        $this->command->info('   ✓ ' . count($jurusanList) . ' Program Studi created');

        // ============================================
        // 6. MATA PELAJARAN MASTER (per jurusan)
        // ============================================
        $this->command->info('📖 Membuat Mata Kuliah...');
        $matkulData = [
            // Teknik Informatika
            1 => [
                ['nama' => 'Algoritma dan Pemrograman', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Struktur Data', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Basis Data', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Pemrograman Web', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Kecerdasan Buatan', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Sistem Informasi
            2 => [
                ['nama' => 'Sistem Informasi Manajemen', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Analisis dan Perancangan Sistem', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'E-Business', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Enterprise Resource Planning', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Business Intelligence', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Teknik Elektro
            3 => [
                ['nama' => 'Rangkaian Listrik', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Elektronika Dasar', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Sistem Kontrol', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Mikroprosesor', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Sistem Tenaga Listrik', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Manajemen
            4 => [
                ['nama' => 'Pengantar Manajemen', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Manajemen Pemasaran', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Manajemen Keuangan', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Manajemen SDM', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Manajemen Operasional', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Akuntansi
            5 => [
                ['nama' => 'Pengantar Akuntansi', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Akuntansi Keuangan', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Akuntansi Biaya', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Audit', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Perpajakan', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Ekonomi Pembangunan
            6 => [
                ['nama' => 'Pengantar Ekonomi Mikro', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Pengantar Ekonomi Makro', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Ekonomi Pembangunan', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Ekonomi Regional', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Ekonomi Internasional', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Ilmu Komunikasi
            7 => [
                ['nama' => 'Pengantar Ilmu Komunikasi', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Komunikasi Massa', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Jurnalistik', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Public Relations', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Broadcasting', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Administrasi Publik
            8 => [
                ['nama' => 'Pengantar Administrasi Publik', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Kebijakan Publik', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Manajemen Pemerintahan', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Administrasi Keuangan Negara', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'E-Government', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
            // Hubungan Internasional
            9 => [
                ['nama' => 'Pengantar Hubungan Internasional', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Politik Internasional', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Diplomasi', 'bobot' => 3, 'jenis' => 'wajib'],
                ['nama' => 'Organisasi Internasional', 'bobot' => 3, 'jenis' => 'peminatan'],
                ['nama' => 'Hukum Internasional', 'bobot' => 3, 'jenis' => 'peminatan'],
            ],
        ];

        $matkulList = [];
        foreach ($matkulData as $jurusanId => $matkuls) {
            foreach ($matkuls as $matkul) {
                $matkulList[] = MataPelajaranMaster::create([
                    'nama' => $matkul['nama'],
                    'id_jurusan' => $jurusanId,
                    'bobot' => $matkul['bobot'],
                    'jenis' => $matkul['jenis'],
                ]);
            }
        }
        $this->command->info('   ✓ ' . count($matkulList) . ' Mata Kuliah Master created');

        // ============================================
        // 7. DOSEN (15 dosen)
        // ============================================
        $this->command->info('👨‍🏫 Membuat Data Dosen...');
        $dosenNamas = [
            'Dr. Ahmad Fauzi, M.Kom', 'Dr. Siti Nurhaliza, M.T', 'Prof. Budi Santoso, Ph.D',
            'Dr. Dewi Lestari, M.M', 'Dr. Eko Prasetyo, M.Ak', 'Dr. Fitri Handayani, M.E',
            'Dr. Gunawan, M.I.Kom', 'Dr. Hendra Wijaya, M.AP', 'Dr. Indah Permata, M.HI',
            'Ir. Joko Susilo, M.T', 'Dra. Kartika Sari, M.M', 'Drs. Lukman Hakim, M.Si',
            'Dr. Maya Anggraini, M.Kom', 'Dr. Nugroho, M.E', 'Dr. Olivia Tan, M.I.Kom',
        ];
        
        $dosenList = [];
        foreach ($dosenNamas as $nama) {
            $dosenList[] = DosenData::create(['nama' => $nama]);
        }
        $this->command->info('   ✓ ' . count($dosenList) . ' Dosen created');

        // ============================================
        // 9. KURIKULUM & KELAS (untuk 2 tahun akademik)
        // ============================================
        $this->command->info('📋 Membuat Kurikulum dan Kelas...');
        
        $kelasList = [];
        $kurikulumList = [];
        
        // Loop untuk 2 tahun akademik (tahun ke-3 dan ke-4 yang aktif)
        for ($tahunIdx = 2; $tahunIdx < 4; $tahunIdx++) {
            $tahun = $tahunAkademik[$tahunIdx];
            
            foreach ($jurusanList as $jurusan) {
                // Buat kurikulum untuk setiap jurusan
                $kurikulum = Kurikulum::create([
                    'nama' => "Kurikulum {$jurusan->nama} {$tahun->nama}",
                    'id_jurusan' => $jurusan->id,
                    'id_tahun_akademik' => $tahun->id,
                    'id_jenjang_pendidikan' => $jenjangS1->id,
                    'status_aktif' => 'Y',
                ]);
                $kurikulumList[] = $kurikulum;

                // Tambahkan mata kuliah ke kurikulum
                $matkulJurusan = array_filter($matkulList, function($m) use ($jurusan) {
                    return $m->id_jurusan == $jurusan->id;
                });

                foreach ($matkulJurusan as $idx => $matkul) {
                    MataPelajaranKurikulum::create([
                        'id_kurikulum' => $kurikulum->id,
                        'id_mata_pelajaran_master' => $matkul->id,
                        'semester' => ($idx < 3) ? 1 : 2, // 3 matkul semester 1, sisanya semester 2
                    ]);
                }

                // Buat kelas untuk program Reguler Pagi dan Reguler Sore
                $programA = ReferenceOption::where('nama_grup', 'program_kelas')->where('kode', 'A')->first();
                $programB = ReferenceOption::where('nama_grup', 'program_kelas')->where('kode', 'B')->first();
                
                foreach ([$programA, $programB] as $program) {
                    if (!$program) continue;
                    
                    $kelas = Kelas::create([
                        'ro_program_kelas' => $program->id,
                        'semester' => 1,
                        'id_jenjang_pendidikan' => $jenjangS1->id,
                        'id_tahun_akademik' => $tahun->id,
                        'id_jurusan' => $jurusan->id,
                        'status_aktif' => 'Y',
                    ]);
                    $kelasList[] = [
                        'kelas' => $kelas,
                        'jurusan' => $jurusan,
                        'kurikulum' => $kurikulum,
                        'tahun' => $tahun,
                        'program' => $program,
                    ];
                }
            }
        }
        $this->command->info('   ✓ ' . count($kelasList) . ' Kelas created');
        $this->command->info('   ✓ ' . count($kurikulumList) . ' Kurikulum created');

        // ============================================
        // 10. MAHASISWA (10 per prodi = 90 total per tahun, 180 total)
        // ============================================
        $this->command->info('👨‍🎓 Membuat Data Mahasiswa...');
        
        $allKrs = [];
        $mahasiswaCounter = 1;
        
        foreach ($kelasList as $kelasData) {
            $kelas = $kelasData['kelas'];
            $jurusan = $kelasData['jurusan'];
            $tahun = $kelasData['tahun'];
            $program = $kelasData['program'];
            
            // Tentukan angkatan berdasarkan tahun akademik
            $angkatan = (strpos($tahun->nama, '2024') !== false) ? 2024 : 2025;
            
            for ($i = 1; $i <= 10; $i++) {
                $nim = $angkatan . str_pad($jurusan->id, 2, '0', STR_PAD_LEFT) . str_pad($mahasiswaCounter, 4, '0', STR_PAD_LEFT);
                
                $siswa = SiswaData::create([
                    'nama' => fake()->name(),
                    'nomor_induk' => $nim,
                ]);

                // Ambil status siswa aktif dari reference_option
                $statusAktif = ReferenceOption::where('nama_grup', 'status_siswa')
                    ->where('nilai', 'Aktif')
                    ->first();

                $riwayat = RiwayatPendidikan::create([
                    'id_siswa_data' => $siswa->id,
                    'id_jenjang_pendidikan' => $jenjangS1->id,
                    'id_jurusan' => $jurusan->id,
                    'ro_status_siswa' => $statusAktif ? $statusAktif->id : null,
                    'angkatan' => $angkatan,
                    'tanggal_mulai' => Carbon::create($angkatan, 9, 1),
                    'tanggal_selesai' => null,
                ]);

                $krs = AkademikKrs::create([
                    'id_riwayat_pendidikan' => $riwayat->id,
                    'id_kelas' => $kelas->id,
                    'semester' => 1,
                    'status_bayar' => 'Y',
                    'jumlah_sks' => 15,
                    'status_aktif' => 'Y',
                ]);
                
                $allKrs[] = [
                    'krs' => $krs,
                    'siswa' => $siswa,
                    'jurusan' => $jurusan,
                ];

                $mahasiswaCounter++;
            }
        }

        $this->command->info('   ✓ Total Mahasiswa: ' . count($allKrs));

        // ============================================
        // 11. MATA PELAJARAN KELAS & JADWAL
        // ============================================
        $this->command->info('📚 Membuat Jadwal Mata Kuliah...');
        
        $mataPelajaranKelasList = [];
        
        foreach ($kelasList as $kelasData) {
            $kelas = $kelasData['kelas'];
            $kurikulum = $kelasData['kurikulum'];
            
            // Ambil mata kuliah dari kurikulum
            $matkulKurikulum = MataPelajaranKurikulum::where('id_kurikulum', $kurikulum->id)->get();
            
            foreach ($matkulKurikulum as $mk) {
                $dosen = $dosenList[array_rand($dosenList)];
                $ruang = ReferenceOption::where('nama_grup', 'ruang_kelas')->inRandomOrder()->first();
                
                $mpKelas = MataPelajaranKelas::create([
                    'id_mata_pelajaran_kurikulum' => $mk->id,
                    'id_kelas' => $kelas->id,
                    'id_dosen_data' => $dosen->id,
                    'uts' => Carbon::now()->addWeeks(8),
                    'uas' => Carbon::now()->addWeeks(16),
                    'ro_ruang_kelas' => $ruang->id,
                ]);
                
                $mataPelajaranKelasList[] = $mpKelas;
            }
        }
        $this->command->info('   ✓ ' . count($mataPelajaranKelasList) . ' Jadwal Mata Kuliah created');

        // ============================================
        // 16. DATA LJK (Lembar Jawaban Komputer / Nilai)
        // ============================================
        $this->command->info('📊 Membuat Data LJK (Nilai)...');
        
        $ljkCounter = 0;
        foreach ($allKrs as $krsData) {
            $krs = $krsData['krs'];
            
            // Ambil semua mata pelajaran untuk kelas ini
            $mataPelajaranKelas = MataPelajaranKelas::where('id_kelas', $krs->id_kelas)->get();
            
            foreach ($mataPelajaranKelas as $mpKelas) {
                $nilai = rand(60, 95) + (rand(0, 99) / 100);
                
                SiswaDataLjk::create([
                    'id_akademik_krs' => $krs->id,
                    'id_mata_pelajaran_kelas' => $mpKelas->id,
                    'nilai' => $nilai,
                ]);
                $ljkCounter++;
            }
        }

        $this->command->info('   ✓ ' . $ljkCounter . ' Data LJK created');
        $this->command->info('');
        $this->command->info('🎉 ========================================');
        $this->command->info('✅ SEEDING SELESAI!');
        $this->command->info('🎉 ========================================');
        $this->command->info('');
        $this->command->info('📊 Ringkasan Data:');
        $this->command->info('   - Jenjang Pendidikan: 1');
        $this->command->info('   - Tahun Akademik: ' . count($tahunAkademik));
        $this->command->info('   - Fakultas: ' . count($fakultasList));
        $this->command->info('   - Program Studi: ' . count($jurusanList));
        $this->command->info('   - Mata Kuliah: ' . count($matkulList));
        $this->command->info('   - Dosen: ' . count($dosenList));
        $this->command->info('   - Kelas: ' . count($kelasList));
        $this->command->info('   - Kurikulum: ' . count($kurikulumList));
        $this->command->info('   - Mahasiswa: ' . count($allKrs));
        $this->command->info('   - Jadwal Mata Kuliah: ' . count($mataPelajaranKelasList));
        $this->command->info('   - Data LJK: ' . $ljkCounter);
        $this->command->info('');
    }
}