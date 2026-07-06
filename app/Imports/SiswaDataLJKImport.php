<?php

namespace App\Imports;

use App\Models\SiswaDataLJK;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaDataLJKImport implements ToCollection, WithHeadingRow
{
    public int $successCount = 0;

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Kita butuh id_krs dan id_mapel_kelas untuk identifikasi
            if (!isset($row['id_krs']) || !isset($row['id_mapel_kelas'])) {
                continue; // Skip baris ini jika tidak valid
            }

            $id_akademik_krs = $row['id_krs'];
            $id_mata_pelajaran_kelas = $row['id_mapel_kelas'];

            // Jika ada 'id_ljk'
            $record = null;
            if (isset($row['id_ljk'])) {
                $record = SiswaDataLJK::find($row['id_ljk']);
            }

            if (!$record) {
                $record = SiswaDataLJK::firstOrNew([
                    'id_akademik_krs' => $id_akademik_krs,
                    'id_mata_pelajaran_kelas' => $id_mata_pelajaran_kelas,
                ]);
            }

            // Update nilai-nilainya
            $record->nilai_uts = $row['uts'] ?? $record->nilai_uts;
            $record->nilai_uas = $row['uas'] ?? $record->nilai_uas;
            $record->nilai_performance = $row['perf'] ?? $record->nilai_performance;

            // Loop untuk TGS_1 s/d TGS_12
            for ($i = 1; $i <= 12; $i++) {
                $key = "tgs_{$i}";
                if (array_key_exists($key, $row->toArray())) {
                    $record->{"nilai_tgs_{$i}"} = $row[$key];
                }
            }

            $record->save();
            $this->successCount++;
        }
    }
}
