<?php

namespace App\Imports;

use App\Models\MataPelajaranKurikulum;
use App\Models\Kurikulum;
use App\Models\MataPelajaranMaster;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MataPelajaranKurikulumImport implements OnEachRow, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public $successCount = 0;

    public function onRow(\Maatwebsite\Excel\Row $excelRow)
    {
        $row = $excelRow->toArray();

        // Skip empty rows
        if (empty(array_filter($row))) {
            return;
        }

        // Resolve IDs based on names (if names are provided instead of IDs)
        $id_kurikulum = null;
        if (isset($row['id_kurikulum'])) {
            $id_kurikulum = $row['id_kurikulum'];
        } elseif (isset($row['kurikulum'])) {
            $kurikulum = Kurikulum::where('nama', $row['kurikulum'])->first();
            if ($kurikulum) {
                $id_kurikulum = $kurikulum->id;
            }
        }

        $id_mata_pelajaran_master = null;
        if (isset($row['id_mata_pelajaran_master'])) {
            $id_mata_pelajaran_master = $row['id_mata_pelajaran_master'];
        } elseif (isset($row['mata_pelajaran_master'])) {
            $mpm = MataPelajaranMaster::where('nama', $row['mata_pelajaran_master'])->first();
            if ($mpm) {
                $id_mata_pelajaran_master = $mpm->id;
            }
        }

        if ($id_kurikulum && $id_mata_pelajaran_master) {
            $updateData = [];
            if (array_key_exists('semester', $row)) {
                $updateData['semester'] = $row['semester'];
            }

            MataPelajaranKurikulum::updateOrCreate(
                [
                    'id_kurikulum' => $id_kurikulum,
                    'id_mata_pelajaran_master' => $id_mata_pelajaran_master,
                ],
                $updateData
            );
            $this->successCount++;
        }
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
