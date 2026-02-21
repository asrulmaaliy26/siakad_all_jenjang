<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MataPelajaranKelasExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    protected Collection $records;
    protected array $selectedColumns;

    /**
     * Kolom beserta label dan keterangan.
     * Kolom yang diawali "_key_" adalah kunci identifikasi untuk import.
     */
    public static function allColumns(): array
    {
        return [
            // === KUNCI IDENTIFIKASI — wajib ada agar bisa diimport kembali ===
            'id'              => 'id',                    // header: 'id' → match guess importer
            'kode_feeder'     => 'kode_feeder',           // header: 'kode_feeder' → match guess importer

            // === DATA REFERENSI (tidak diimport, hanya informasi) ===
            'mata_pelajaran'  => 'Nama Mata Pelajaran',
            'program_kelas'   => 'Program Kelas',
            'dosen_nama'      => 'Nama Dosen',
            'ruang'           => 'Ruang Kelas',
            'pelaksanaan'     => 'Pelaksanaan',

            // === DATA YANG BISA DIEDIT & DIIMPORT KEMBALI ===
            'id_dosen_data'   => 'id_dosen_data',         // header: 'id_dosen_data' → match guess importer
            'jumlah'          => 'jumlah',                // header sederhana = nama field langsung match
            'hari'            => 'hari',
            'tanggal'         => 'tanggal',
            'jam'             => 'jam',
            'uts'             => 'uts',
            'uas'             => 'uas',
            'status_uts'      => 'status_uts',
            'status_uas'      => 'status_uas',
            'ruang_uts'       => 'ruang_uts',
            'ruang_uas'       => 'ruang_uas',
            'link_kelas'      => 'link_kelas',
            'passcode'        => 'passcode',
        ];
    }

    public function __construct(Collection $records, array $selectedColumns)
    {
        $this->records = $records;
        $this->selectedColumns = $selectedColumns;
    }

    public function collection(): Collection
    {
        return $this->records;
    }

    public function headings(): array
    {
        $all = self::allColumns();
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
            'id'             => $record->id,
            'kode_feeder'    => $record->mataPelajaranKurikulum?->mataPelajaranMaster?->kode_feeder ?? '',
            'mata_pelajaran' => $record->mataPelajaranKurikulum?->mataPelajaranMaster?->nama ?? '',
            'program_kelas'  => $record->kelas?->programKelas?->nilai ?? '',
            'dosen_nama'     => $record->dosen?->nama ?? '',
            'id_dosen_data'  => $record->id_dosen_data ?? '',
            'ruang'          => $record->ruangKelas?->nilai ?? '',
            'pelaksanaan'    => $record->pelaksanaanKelas?->nilai ?? '',
            'jumlah'         => $record->jumlah,
            'hari'           => $record->hari,
            'tanggal'        => $record->tanggal ? \Carbon\Carbon::parse($record->tanggal)->format('Y-m-d') : '',
            'jam'            => $record->jam,
            'uts'            => $record->uts ? \Carbon\Carbon::parse($record->uts)->format('Y-m-d H:i:s') : '',
            'uas'            => $record->uas ? \Carbon\Carbon::parse($record->uas)->format('Y-m-d H:i:s') : '',
            'status_uts'     => $record->status_uts ?? '',
            'status_uas'     => $record->status_uas ?? '',
            'ruang_uts'      => $record->ruang_uts ?? '',
            'ruang_uas'      => $record->ruang_uas ?? '',
            'link_kelas'     => $record->link_kelas ?? '',
            'passcode'       => $record->passcode ?? '',
            default          => '',
        };
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws      = $event->sheet->getDelegate();
                $lastCol = $ws->getHighestColumn();

                // Header: background biru tua, teks putih
                $ws->getStyle("A1:{$lastCol}1")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FF1565C0');

                $ws->getStyle("A1:{$lastCol}1")
                    ->getFont()
                    ->getColor()
                    ->setARGB('FFFFFFFF');

                // Freeze baris header
                $ws->freezePane('A2');

                // Beri warna kuning pada kolom kunci (A & B = ID + kode_feeder)
                // agar user tahu tidak menghapus kolom ini
                $lastRow = $ws->getHighestRow();
                if ($lastRow > 1) {
                    // Kolom A (ID) - warna kuning muda
                    $ws->getStyle("A2:A{$lastRow}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFFFF9C4');

                    // Kolom B (kode_feeder) - warna hijau muda
                    $ws->getStyle("B2:B{$lastRow}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFC8E6C9');
                }
            },
        ];
    }
}
