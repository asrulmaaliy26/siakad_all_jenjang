<?php

namespace App\Exports;

use App\Models\MataPelajaranMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MataPelajaranMasterTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $jurusanOptions = \App\Models\Jurusan::query()
            ->with(['jenjangPendidikan'])
            ->get()
            ->map(fn($item) => $item->id . ' - ' . $item->nama); // Format: ID - Nama Jurusan

        $exampleData = collect([
            [
                'kode_feeder' => 'KD001',
                'nama' => 'Contoh Mata Pelajaran',
                'jurusan' => '1', // ID of the major
                'bobot' => '3',
                'jenis' => 'Wajib'
            ]
        ]);

        // Merge example data with list of majors
        $exportData = collect();
        $maxRows = max($exampleData->count(), $jurusanOptions->count());

        for ($i = 0; $i < $maxRows; $i++) {
            $row = $exampleData->get($i, [
                'kode_feeder' => null,
                'nama' => null,
                'jurusan' => null,
                'bobot' => null,
                'jenis' => null,
            ]);

            // Add spacer columns if necessary, but here we just append to the array
            $row['separator'] = ''; // Empty separator column

            // Add reference data
            $jurusan = $jurusanOptions->values()->get($i);
            $row['ref_jurusan_nama'] = $jurusan;

            $exportData->push($row);
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'kode_feeder',
            'nama',
            'jurusan',
            'bobot',
            'jenis',
            '', // Separator
            'Referensi Jurusan (ID - Nama)',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
