<?php

namespace App\Imports;

use App\Models\MataPelajaranMaster;
use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MataPelajaranMasterImport implements OnEachRow, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public $successCount = 0;

    public function onRow(\Maatwebsite\Excel\Row $excelRow)
    {
        $row = $excelRow->toArray();

        // Skip empty rows
        if (empty(array_filter($row))) {
            return;
        }

        // Resolve Jurusan ID if name is provided instead of ID
        $id_jurusan = null;
        if (isset($row['id_jurusan'])) {
            $id_jurusan = $row['id_jurusan'];
        } elseif (isset($row['jurusan'])) {
            $jurusan = Jurusan::where('nama', $row['jurusan'])->first();
            if ($jurusan) {
                $id_jurusan = $jurusan->id;
            }
        }

        if (isset($row['nama'])) {
            $updateData = [];
            $fields = ['kode_feeder', 'id_jurusan', 'bobot', 'jenis'];
            foreach ($fields as $field) {
                if (array_key_exists($field, $row)) {
                    $updateData[$field] = $row[$field];
                }
            }
            if ($id_jurusan) {
                $updateData['id_jurusan'] = $id_jurusan;
            }

            MataPelajaranMaster::updateOrCreate(
                ['nama' => $row['nama']],
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
