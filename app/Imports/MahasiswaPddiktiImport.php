<?php

namespace App\Imports;

use App\Models\SiswaData;
use App\Models\RiwayatPendidikan;
use App\Models\SiswaDataOrangTua;
use App\Models\SiswaDataPendaftar;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class MahasiswaPddiktiImport implements ToCollection, WithStartRow, WithChunkReading
{
    public function startRow(): int
    {
        return 2; // Skip header
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nim = trim($row[0] ?? "");
            $nama = trim($row[1] ?? "");
            $nik = trim($row[5] ?? "");
            
            // Skip jika NIK dan NIM kosong
            if (empty($nik) && empty($nim)) {
                continue;
            }

            // --- 1. SISWA DATA ---
            $siswa = null;
            if (!empty($nik)) {
                $siswa = SiswaData::where("no_ktp", $nik)->first();
            }
            if (!$siswa && !empty($nama)) {
                // Fallback cari by Nama dan Tgl Lahir
                $tglLahirFormat = $this->parseDate($row[3]);
                $siswaQuery = SiswaData::where("nama_lengkap", $nama);
                if ($tglLahirFormat) {
                    $siswaQuery->where("tanggal_lahir", $tglLahirFormat);
                }
                $siswa = $siswaQuery->first();
            }

            if (!$siswa) {
                $siswa = new SiswaData();
                $siswa->nama_lengkap = $nama;
                $siswa->nama = $nama;
                $siswa->no_ktp = $nik;
            }

            // Update data profil
            $siswa->kota_lahir = $row[2] ?? $siswa->kota_lahir;
            $siswa->tanggal_lahir = $this->parseDate($row[3]) ?? $siswa->tanggal_lahir;
            $siswa->jenis_kelamin = $row[4] ?? $siswa->jenis_kelamin;
            $siswa->agama = $row[6] ?? $siswa->agama;
            $siswa->nisn = $row[7] ?? $siswa->nisn;
            $siswa->npwp = $row[9] ?? $siswa->npwp;
            $siswa->kewarganegaraan = ($row[10] === "ID") ? "WNI" : ($row[10] ?? $siswa->kewarganegaraan);
            $siswa->alamat = $row[14] ?? $siswa->alamat;
            $siswa->rt = $row[15] ?? $siswa->rt;
            $siswa->rw = $row[16] ?? $siswa->rw;
            $siswa->dusun = $row[17] ?? $siswa->dusun;
            $siswa->desa = $row[18] ?? $siswa->desa;
            $siswa->kecamatan = (!empty($row[19]) && $row[19] !== "000000") ? $row[19] : $siswa->kecamatan;
            $siswa->kode_pos = $row[20] ?? $siswa->kode_pos;
            $siswa->jenis_domisili = $row[21] ?? $siswa->jenis_domisili;
            $siswa->transportasi = $row[22] ?? $siswa->transportasi;
            $siswa->no_telepon = $row[23] ?? $siswa->no_telepon;
            $siswa->no_hp = $row[24] ?? $siswa->no_hp;
            $siswa->email = $row[25] ?? $siswa->email;
            $siswa->penerima_kps = $row[26] ?? $siswa->penerima_kps;
            $siswa->no_kps = $row[27] ?? $siswa->no_kps;
            $siswa->save();

            // --- 2. USER LOGIN ---
            if (!$siswa->user_id) {
                $emailUser = "mhs" . $siswa->id . "@student.siakad.com";
                $user = User::where("email", $emailUser)->first();
                if (!$user) {
                    $user = User::create([
                        "name" => $siswa->nama_lengkap ?: ("Mahasiswa " . $siswa->id),
                        "email" => $emailUser,
                        "password" => Hash::make("password"),
                        "view_password" => "password",
                    ]);
                    if (Role::where("name", "murid")->exists()) {
                        $user->assignRole("murid");
                    }
                }
                $siswa->user_id = $user->id;
                $siswa->save();
            }

            // --- 3. ORANG TUA ---
            $ortu = SiswaDataOrangTua::firstOrNew(["id_siswa_data" => $siswa->id]);
            $ortu->Nomor_KTP_Ayah = $row[28] ?? $ortu->Nomor_KTP_Ayah;
            $ortu->Nama_Ayah = $row[29] ?? $ortu->Nama_Ayah;
            $ortu->Tgl_Lhr_Ayah = $this->parseDate($row[30]) ?? $ortu->Tgl_Lhr_Ayah;
            $ortu->Pendidikan_Terakhir_Ayah = $row[31] ?? $ortu->Pendidikan_Terakhir_Ayah;
            $ortu->Pekerjaan_Ayah = $row[32] ?? $ortu->Pekerjaan_Ayah;
            $ortu->Penghasilan_Ayah = $row[33] ?? $ortu->Penghasilan_Ayah;
            $ortu->Nomor_KTP_Ibu = $row[34] ?? $ortu->Nomor_KTP_Ibu;
            $ortu->Nama_Ibu = $row[35] ?? $ortu->Nama_Ibu;
            $ortu->Tgl_Lhr_Ibu = $this->parseDate($row[36]) ?? $ortu->Tgl_Lhr_Ibu;
            $ortu->Pendidikan_Terakhir_Ibu = $row[37] ?? $ortu->Pendidikan_Terakhir_Ibu;
            $ortu->Pekerjaan_Ibu = $row[38] ?? $ortu->Pekerjaan_Ibu;
            $ortu->Penghasilan_Ibu = $row[39] ?? $ortu->Penghasilan_Ibu;
            $ortu->nama_wali = $row[40] ?? $ortu->nama_wali;
            $ortu->tgl_lahir_wali = $this->parseDate($row[41]) ?? $ortu->tgl_lahir_wali;
            $ortu->pendidikan_wali = $row[42] ?? $ortu->pendidikan_wali;
            $ortu->pekerjaan_wali = $row[43] ?? $ortu->pekerjaan_wali;
            $ortu->penghasilan_wali = $row[44] ?? $ortu->penghasilan_wali;
            $ortu->save();

            // --- 4. RIWAYAT PENDIDIKAN ---
            if (!empty($nim)) {
                $riwayat = RiwayatPendidikan::where("id_siswa_data", $siswa->id)->where("nomor_induk", $nim)->first();
                if (!$riwayat) {
                    $riwayat = RiwayatPendidikan::where("id_siswa_data", $siswa->id)->latest()->first();
                }
                if (!$riwayat) {
                    $riwayat = new RiwayatPendidikan();
                    $riwayat->id_siswa_data = $siswa->id;
                    $riwayat->status = "Aktif";
                }
                
                $riwayat->nomor_induk = $nim;
                $riwayat->ro_jns_daftar = $row[11] ?? $riwayat->ro_jns_daftar;
                $riwayat->tanggal_mulai = $this->parseDate($row[12]) ?? $riwayat->tanggal_mulai;
                
                // Cari Tahun Akademik via Mulai Semester (N) misal 20241
                if (!empty($row[13])) {
                    $ta = TahunAkademik::where("kode_pddikti", trim($row[13]))->first();
                    if ($ta) {
                        $riwayat->id_tahun_akademik = $ta->id;
                    }
                    $riwayat->mulai_smt = trim($row[13]);
                }

                // Cari Jurusan via Kode Prodi (AT)
                if (!empty($row[45])) {
                    $jurusan = Jurusan::where("kode_dikti", trim($row[45]))->first();
                    if ($jurusan) {
                        $riwayat->id_jurusan = $jurusan->id;
                    }
                }

                $riwayat->sks_diakui = $row[47] ?? $riwayat->sks_diakui;
                $riwayat->kode_pt_asal = $row[48] ?? $riwayat->kode_pt_asal;
                $riwayat->nm_pt_asal = $row[49] ?? $riwayat->nm_pt_asal;
                $riwayat->kode_prodi_asal = $row[50] ?? $riwayat->kode_prodi_asal;
                $riwayat->nm_prodi_asal = $row[51] ?? $riwayat->nm_prodi_asal;
                $riwayat->pembiayaan = $row[52] ?? $riwayat->pembiayaan;
                $riwayat->save();
            }

            // --- 5. SISWA DATA PENDAFTAR ---
            if (!empty($row[8]) || !empty($row[53])) {
                $pendaftar = SiswaDataPendaftar::firstOrNew(["id_siswa_data" => $siswa->id]);
                if (!empty($row[8])) $pendaftar->Jalur_PMB = $row[8]; // Jalur Pendaftaran (I)
                if (!empty($row[53])) $pendaftar->Biaya_Pendaftaran = $row[53]; // Biaya Masuk (BB)
                $pendaftar->save();
            }
        }
    }

    private function parseDate($val)
    {
        if (empty($val)) return null;
        if (is_numeric($val)) {
            // Excel numeric date
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format("Y-m-d");
            } catch (\Exception $e) {
                return null;
            }
        }
        try {
            return Carbon::parse($val)->format("Y-m-d");
        } catch (\Exception $e) {
            return null;
        }
    }
}

