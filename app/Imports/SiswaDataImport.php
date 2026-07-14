<?php

namespace App\Imports;

use App\Models\SiswaData;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaDataImport implements OnEachRow, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public $successCount = 0;

    public function onRow(\Maatwebsite\Excel\Row $excelRow)
    {
        $row = $excelRow->toArray();

        // Skip empty rows (jika tidak ada data sama sekali)
        if (empty(array_filter($row))) {
            return;
        }

        $id = $row['id'] ?? null;
        
        $fields = [
            'nama', 'nama_lengkap', 'jenis_kelamin', 'golongan_darah', 'kota_lahir', 'tanggal_lahir', 
            'alamat', 'nomor_rumah', 'dusun', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'kode_pos', 
            'provinsi', 'tempat_domisili', 'jenis_domisili', 'no_telepon', 'no_ktp', 'no_kk', 'agama', 
            'kewarganegaraan', 'kode_negara', 'status_pkawin', 'pekerjaan', 'biaya_ditanggung', 
            'transportasi', 'status_asal_sekolah', 'asal_slta', 'jenis_slta', 'kejuruan_slta', 
            'alamat_lengkap_sekolah_asal', 'tahun_lulus_slta', 'nomor_seri_ijazah_slta', 'nisn', 
            'anak_ke', 'jumlah_saudara', 'email', 'penerima_kps', 'no_kps', 'kebutuhan_khusus', 'status_siswa'
        ];

        $updateData = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $row)) {
                $val = $row[$field];
                // Konversi tanggal dari format serial Excel jika field adalah tanggal_lahir
                if (in_array($field, ['tanggal_lahir']) && is_numeric($val)) {
                    try {
                        $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Biarkan aslinya jika gagal dikonversi
                    }
                }
                $updateData[$field] = $val;
            }
        }

        if ($id) {
            SiswaData::updateOrCreate(['id' => $id], $updateData);
        } else {
            // Coba update berdasarkan NIK atau NISN jika ID tidak ada
            if (!empty($row['no_ktp'])) {
                SiswaData::updateOrCreate(['no_ktp' => $row['no_ktp']], $updateData);
            } elseif (!empty($row['nisn'])) {
                SiswaData::updateOrCreate(['nisn' => $row['nisn']], $updateData);
            } else {
                // Buat baru jika belum ada
                if (!empty($updateData['nama']) || !empty($updateData['nama_lengkap'])) {
                    SiswaData::create($updateData);
                }
            }
        }

        $this->successCount++;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
