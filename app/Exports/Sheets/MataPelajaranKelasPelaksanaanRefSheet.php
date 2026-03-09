<?php

namespace App\Exports\Sheets;

use App\Models\RefOption\PelaksanaanKelas;
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

class MataPelajaranKelasPelaksanaanRefSheet implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    public function title(): string
    {
        return 'REF - Pelaksanaan';
    }

    public function collection()
    {
        try {
            return PelaksanaanKelas::orderBy('nilai')->get()->map(fn($r) => [
                $r->id,
                $r->nilai,
            ]);
        } catch (\Throwable $e) {
            Log::error('REF Pelaksanaan sheet gagal', ['error' => $e->getMessage()]);
            return collect([['ERROR', $e->getMessage()]]);
        }
    }

    public function headings(): array
    {
        return ['ro_pelaksanaan_kelas (isi di sheet Data)', 'Nama Pelaksanaan'];
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
                    ->getStartColor()->setARGB('FF6A1B9A');
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
