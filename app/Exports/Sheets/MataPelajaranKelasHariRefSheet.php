<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MataPelajaranKelasHariRefSheet implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    public function title(): string
    {
        return 'REF - Hari';
    }

    public function collection()
    {
        return collect([
            ['Senin'],
            ['Selasa'],
            ['Rabu'],
            ['Kamis'],
            ['Jumat'],
            ['Sabtu'],
            ['Minggu'],
        ]);
    }

    public function headings(): array
    {
        return ['hari (isi di sheet Data — gunakan nilai ini persis)'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0277BD']],
                'font' => ['color' => ['argb' => 'FFFFFFFF'], 'bold' => true],
            ],
        ];
    }
}
