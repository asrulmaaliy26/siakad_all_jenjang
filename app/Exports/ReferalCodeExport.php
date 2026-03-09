<?php

namespace App\Exports;

use App\Models\ReferalCode;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class ReferalCodeExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithDrawings,
    WithStyles,
    ShouldAutoSize
{
    protected Collection $records;
    protected array $tempFiles = [];

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kode',
            'Type',
            'Status',
            'Link Pendaftaran',
            'QR Code'
        ];
    }

    public function map($record): array
    {
        return [
            $record->nama,
            $record->kode,
            $record->type,
            $record->status,
            url('/pendaftaran?ref=' . $record->kode),
            '' // Kosongkan untuk kolom QR Code yang akan diisi oleh drawing
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $rowIndex = 2; // Mulai dari baris 2 (setelah header)

        foreach ($this->records as $record) {
            $url = url('/pendaftaran?ref=' . $record->kode);
            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($url) . "&margin=10";

            try {
                // Gunakan timeout lebih lama agar stabil saat export banyak data
                $response = Http::timeout(10)->get($qrApiUrl);

                if ($response->successful()) {
                    $tempDir = storage_path('app/temp_qr');
                    if (!File::exists($tempDir)) {
                        File::makeDirectory($tempDir, 0755, true);
                    }

                    // Gunakan nama file yang sangat unik
                    $tempPath = $tempDir . '/qr_' . $record->id . '_' . uniqid() . '.png';
                    File::put($tempPath, $response->body());
                    $this->tempFiles[] = $tempPath;

                    $drawing = new Drawing();
                    $drawing->setName('QR-' . $record->kode);
                    $drawing->setDescription($record->kode);
                    $drawing->setPath($tempPath);
                    $drawing->setHeight(100); // Sesuaikan dengan row height
                    $drawing->setCoordinates('F' . $rowIndex); // Kolom F adalah QR Code
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);

                    $drawings[] = $drawing;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal export QR untuk {$record->kode}: " . $e->getMessage());
            }

            $rowIndex++;
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        // Set row height tinggi untuk semua baris data agar gambar QR Code muat
        $rowCount = count($this->records) + 1;
        for ($i = 2; $i <= $rowCount; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(85); // 85 points cukup untuk image height 100px
        }

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ],
            // Beri border untuk semua data
            "A1:F{$rowCount}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function __destruct()
    {
        // Bersihkan file temporary setelah selesai
        foreach ($this->tempFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }
}
