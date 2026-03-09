<?php

namespace App\Exports\Sheets;

use App\Models\DosenData;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MataPelajaranKelasDosenRefSheet implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    public function title(): string
    {
        return 'REF - Dosen';
    }

    public function collection()
    {
        try {
            return DosenData::with('jurusan')
                ->orderBy('id_jurusan')
                ->orderBy('nama')
                ->get()
                ->map(fn($d) => [
                    $d->id,
                    $d->nama,
                    $d->NIPDN ?? $d->NIY ?? '-',
                    $d->jurusan?->nama ?? '-',
                    $d->id_jurusan ?? '-',
                ]);
        } catch (\Throwable $e) {
            Log::error('REF Dosen sheet gagal', ['error' => $e->getMessage()]);
            return collect([['ERROR', $e->getMessage(), '-', '-', '-']]);
        }
    }

    public function headings(): array
    {
        return ['id_dosen_data (isi di sheet Data)', 'Nama Dosen', 'NIPDN / NIY', 'Jurusan', 'ID Jurusan'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws      = $event->sheet->getDelegate();
                $lastCol = $ws->getHighestColumn();
                $lastRow = $ws->getHighestRow();

                $ws->getStyle("A1:{$lastCol}1")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4CAF50');
                $ws->getStyle("A1:{$lastCol}1")
                    ->getFont()->getColor()->setARGB('FFFFFFFF');
                $ws->getStyle("A1:{$lastCol}1")
                    ->getFont()->setBold(true);

                if ($lastRow > 1) {
                    $ws->getStyle("A1:{$lastCol}{$lastRow}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                    $ws->getStyle("A2:A{$lastRow}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFFFF9C4');
                }
                $ws->freezePane('A2');
            },
        ];
    }
}
