<?php

namespace App\Exports;

use App\Models\SiswaData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaPddiktiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $records;

    public function __construct($records = null)
    {
        $this->records = $records;
    }

    public function collection()
    {
        if ($this->records) {
            $this->records->loadMissing(["riwayatPendidikanAktif.jurusan", "pendaftar", "orangTua", "riwayatPendidikanAktif.tahunAkademik"]);
            return $this->records;
        }
        return SiswaData::with(["riwayatPendidikanAktif.jurusan", "pendaftar", "orangTua", "riwayatPendidikanAktif.tahunAkademik"])->get();
    }

    public function headings(): array
    {
        return [
            "NIM", "Nama", "Tempat Lahir", "Tanggal Lahir", "Jenis Kelamin", "NIK", "Agama", "NISN", "Jalur Pendaftaran", "NPWP", 
            "Kewarganegaraan", "Jenis Pendaftaran", "Tanggal Masuk Kuliah", "Mulai Semester", "Jalan", "RT", "RW", "Nama Dusun", 
            "Kelurahan", "Kecamatan", "Kode Pos", "Jenis Tinggal", "Alat Transportasi", "Telp Rumah", "No HP", "Email", "Terima KPS", 
            "No KPS", "NIK Ayah", "Nama Ayah", "Tanggal Lahir Ayah", "Pendidikan Ayah", "Pekerjaan Ayah", "Penghasilan Ayah", 
            "NIK Ibu", "Nama Ibu", "Tanggal Lahir Ibu", "Pendidikan Ibu", "Pekerjaan Ibu", "Penghasilan Ibu", "Nama Wali", 
            "Tanggal Lahir Wali", "Pendidikan Wali", "Pekerjaan Wali", "Penghasilan Wali", "Kode Prodi", "Nama Prodi", "SKS Diakui", 
            "Kode PT Asal", "Nama PT Asal", "Kode Prodi Asal", "Nama Prodi Asal", "Jenis Pembiayaan", "Jumlah Biaya Masuk", 
            "ID Wilayah (Internal, Jangan Diubah)", "", ""
        ];
    }

    public function map($siswa): array
    {
        $riwayat = $siswa->riwayatPendidikanAktif;
        $orangTua = $siswa->orangTua;
        $pendaftar = $siswa->pendaftar;
        $jurusan = $riwayat ? $riwayat->jurusan : null;
        $ta = $riwayat ? $riwayat->tahunAkademik : null;

        // helper for matching array keys
        $match = function($val, $arr) {
            if (!$val) return "";
            return array_search($val, $arr) ?: $val;
        };

        return [
            $riwayat ? $riwayat->nomor_induk : "", // A: NIM
            $siswa->nama_lengkap, // B: Nama
            $siswa->kota_lahir, // C: Tempat Lahir
            $siswa->tanggal_lahir, // D: Tanggal Lahir
            $siswa->jenis_kelamin, // E: Jenis Kelamin
            $siswa->no_ktp, // F: NIK
            $siswa->agama, // G: Agama
            $siswa->nisn, // H: NISN
            $pendaftar ? $pendaftar->Jalur_PMB : "", // I: Jalur Pendaftaran (Hardcode/ID PMB) - the user uses RO, we just output the ID
            $siswa->npwp, // J: NPWP
            strtoupper($siswa->kewarganegaraan) === 'WNI' ? 'ID' : ($siswa->kewarganegaraan ?: 'ID'), // K: Kewarganegaraan
            $riwayat ? $riwayat->ro_jns_daftar : "", // L: Jenis Pendaftaran
            $riwayat ? $riwayat->tanggal_mulai : "", // M: Tanggal Masuk Kuliah
            $riwayat ? $riwayat->mulai_smt : ($ta ? $ta->kode_pddikti : ""), // N: Mulai Semester
            $siswa->alamat, // O: Jalan
            $siswa->rt, // P: RT
            $siswa->rw, // Q: RW
            $siswa->dusun, // R: Dusun
            $siswa->desa, // S: Kelurahan
            $siswa->kecamatan ?: "000000", // T: Kecamatan (id_wil)
            $siswa->kode_pos, // U: Kode Pos
            $siswa->jenis_domisili, // V: Jenis Tinggal (id)
            $siswa->transportasi, // W: Alat Transportasi (id)
            $siswa->no_telepon, // X: Telp Rumah
            $siswa->no_hp, // Y: No HP
            $siswa->email, // Z: Email
            $siswa->penerima_kps, // AA: Terima KPS
            $siswa->no_kps, // AB: No KPS
            
            // Ayah
            $orangTua ? $orangTua->Nomor_KTP_Ayah : "", // AC: NIK Ayah
            $orangTua ? $orangTua->Nama_Ayah : "", // AD: Nama Ayah
            $orangTua ? $orangTua->Tgl_Lhr_Ayah : "", // AE: Tanggal Lahir Ayah
            $orangTua ? $orangTua->Pendidikan_Terakhir_Ayah : "", // AF: Pendidikan Ayah
            $orangTua ? $orangTua->Pekerjaan_Ayah : "", // AG: Pekerjaan Ayah
            $orangTua ? $orangTua->Penghasilan_Ayah : "", // AH: Penghasilan Ayah
            
            // Ibu
            $orangTua ? $orangTua->Nomor_KTP_Ibu : "", // AI: NIK Ibu
            $orangTua ? $orangTua->Nama_Ibu : "", // AJ: Nama Ibu
            $orangTua ? $orangTua->Tgl_Lhr_Ibu : "", // AK: Tanggal Lahir Ibu
            $orangTua ? $orangTua->Pendidikan_Terakhir_Ibu : "", // AL: Pendidikan Ibu
            $orangTua ? $orangTua->Pekerjaan_Ibu : "", // AM: Pekerjaan Ibu
            $orangTua ? $orangTua->Penghasilan_Ibu : "", // AN: Penghasilan Ibu
            
            // Wali
            $orangTua ? $orangTua->nama_wali : "", // AO: Nama Wali
            $orangTua ? $orangTua->tgl_lahir_wali : "", // AP: Tanggal Lahir Wali
            $orangTua ? $orangTua->pendidikan_wali : "", // AQ: Pendidikan Wali
            $orangTua ? $orangTua->pekerjaan_wali : "", // AR: Pekerjaan Wali
            $orangTua ? $orangTua->penghasilan_wali : "", // AS: Penghasilan Wali
            
            // PT/Prodi
            $jurusan ? $jurusan->kode_dikti : "", // AT: Kode Prodi
            $jurusan ? $jurusan->nama_jurusan : "", // AU: Nama Prodi
            $riwayat ? $riwayat->sks_diakui : "", // AV: SKS Diakui
            $riwayat ? $riwayat->kode_pt_asal : "", // AW: Kode PT Asal
            $riwayat ? $riwayat->nm_pt_asal : "", // AX: Nama PT Asal
            $riwayat ? $riwayat->kode_prodi_asal : "", // AY: Kode Prodi Asal
            $riwayat ? $riwayat->nm_prodi_asal : "", // AZ: Nama Prodi Asal
            
            $riwayat ? $riwayat->pembiayaan : "", // BA: Jenis Pembiayaan
            $pendaftar ? $pendaftar->Biaya_Pendaftaran : "", // BB: Jumlah Biaya Masuk
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ["font" => ["bold" => true, "color" => ["argb" => "FFFFFFFF"]], "fill" => ["fillType" => "solid", "startColor" => ["argb" => "FFFF0000"]]],
        ];
    }
}

