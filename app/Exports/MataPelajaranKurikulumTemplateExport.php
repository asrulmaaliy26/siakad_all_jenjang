<?php

namespace App\Exports;

use App\Models\MataPelajaranKurikulum;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MataPelajaranKurikulumTemplateExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([
            [
                'kurikulum' => 'Kurikulum 2024',
                'mataPelajaranMaster' => 'Matematika Dasar',
                'semester' => '1',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'kurikulum',
            'mataPelajaranMaster',
            'semester',
        ];
    }
}
