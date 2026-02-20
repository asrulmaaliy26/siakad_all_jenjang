<?php

namespace App\Exports;

use App\Models\MataPelajaranMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MataPelajaranMasterTemplateExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Example data matching the Importer columns
        return collect([
            [
                'kode_feeder' => 'KD001',
                'nama' => 'Contoh Mata Pelajaran',
                'jurusan' => 'Teknik Informatika', // Example Major Name
                'bobot' => '3',
                'jenis' => 'Wajib'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'kode_feeder',
            'nama',
            'jurusan',
            'bobot',
            'jenis',
        ];
    }
}
