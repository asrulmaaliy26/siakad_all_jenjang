<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Exports\MataPelajaranKelasExport;

class MataPelajaranKelasDataSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    protected Collection $records;
    protected array $selectedColumns;

    public function __construct(Collection $records, array $selectedColumns)
    {
        $this->records         = $records;
        $this->selectedColumns = $selectedColumns;
    }

    public function title(): string
    {
        return 'Data Matkul';
    }

    public function collection(): Collection
    {
        return $this->records;
    }

    public function headings(): array
    {
        $all = MataPelajaranKelasExport::allColumns();
        return array_map(fn($key) => $all[$key] ?? ucfirst(str_replace('_', ' ', $key)), $this->selectedColumns);
    }

    public function map($record): array
    {
        $row = [];
        foreach ($this->selectedColumns as $col) {
            $row[] = $this->getValue($record, $col);
        }
        return $row;
    }

    protected function getValue($record, string $col): mixed
    {
        return match ($col) {
            'id'                   => $record->id,
            'kode_feeder'          => $record->mataPelajaranKurikulum?->mataPelajaranMaster?->kode_feeder ?? '',
            'mata_pelajaran'       => $record->mataPelajaranKurikulum?->mataPelajaranMaster?->nama ?? '',
            'program_kelas'        => $record->kelas?->programKelas?->nilai ?? '',
            'dosen_nama'           => $record->dosen?->nama ?? '',
            'id_dosen_data'        => $record->id_dosen_data ?? '',
            'ro_ruang_kelas'       => $record->ro_ruang_kelas ?? '',
            'ro_pelaksanaan_kelas' => $record->ro_pelaksanaan_kelas ?? '',
            'ruang'                => $record->ruangKelas?->nilai ?? '',
            'pelaksanaan'          => $record->pelaksanaanKelas?->nilai ?? '',
            'jumlah'               => $record->jumlah,
            'hari'                 => $record->hari,
            'tanggal'              => $record->tanggal ? \Carbon\Carbon::parse($record->tanggal)->format('Y-m-d') : '',
            'jam'                  => $record->jam,
            'uts'                  => $record->uts ? \Carbon\Carbon::parse($record->uts)->format('Y-m-d H:i:s') : '',
            'uas'                  => $record->uas ? \Carbon\Carbon::parse($record->uas)->format('Y-m-d H:i:s') : '',
            'status_uts'           => $record->status_uts ?? '',
            'status_uas'           => $record->status_uas ?? '',
            'ruang_uts'            => $record->ruang_uts ?? '',
            'ruang_uas'            => $record->ruang_uas ?? '',
            'link_kelas'           => $record->link_kelas ?? '',
            'passcode'             => $record->passcode ?? '',
            default                => '',
        };
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'size' => 11]]];
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
                    ->getStartColor()->setARGB('FF1565C0');
                $ws->getStyle("A1:{$lastCol}1")
                    ->getFont()->getColor()->setARGB('FFFFFFFF');
                $ws->freezePane('A2');

                if ($lastRow > 1) {
                    $ws->getStyle("A2:A{$lastRow}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFFFF9C4');
                    $ws->getStyle("B2:B{$lastRow}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFC8E6C9');
                }
            },
        ];
    }
}
