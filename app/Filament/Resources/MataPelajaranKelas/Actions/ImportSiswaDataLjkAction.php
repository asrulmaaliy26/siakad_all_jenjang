<?php

namespace App\Filament\Resources\MataPelajaranKelas\Actions;

use App\Models\SiswaDataLJK;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\CSV\Options as CsvOptions;

/**
 * Custom Import Action for Student LJK Data (Grades).
 * Processes Excel/CSV files directly using OpenSpout.
 */
class ImportSiswaDataLjkAction
{
    public static function make(string $name = 'import_siswa_data_ljk'): Action
    {
        return Action::make($name)
            ->label('Import Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->modalHeading('Import Data Nilai Mahasiswa')
            ->modalDescription(
                'Upload file Excel (.xlsx) atau CSV yang berisi data nilai. ' .
                    'Data akan dicocokkan berdasarkan ID LJK atau ID KRS + ID Mapel Kelas.'
            )
            ->modalSubmitActionLabel('Proses Import')
            ->form([
                FileUpload::make('import_file')
                    ->label('File Excel / CSV')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'text/csv',
                        'application/csv',
                        'text/plain',
                    ])
                    ->disk('local')
                    ->directory('imports/siswa-data-ljk')
                    ->required()
                    ->helperText('Format yang diterima: .xlsx atau .csv. Pastikan baris pertama adalah header.'),
            ])
            ->action(function (array $data, $livewire) {
                $filePath = $data['import_file'] ?? null;

                if (!$filePath) {
                    Notification::make()
                        ->title('File tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $fullPath = Storage::disk('local')->path($filePath);

                if (!file_exists($fullPath)) {
                    Notification::make()
                        ->title('File tidak bisa dibaca')
                        ->danger()
                        ->send();
                    return;
                }

                try {
                    $result = self::processFile($fullPath);
                } catch (\Throwable $e) {
                    Log::error('[ImportSiswaDataLjk] Error: ' . $e->getMessage());
                    Notification::make()
                        ->title('Gagal memproses file')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                    return;
                } finally {
                    Storage::disk('local')->delete($filePath);
                }

                $msg = "✅ {$result['updated']} data diperbarui.";
                if ($result['skipped'] > 0) {
                    $msg .= " ⏭️ {$result['skipped']} data dilewati.";
                }
                if ($result['errors'] > 0) {
                    $msg .= " ❌ {$result['errors']} data gagal.";
                }

                Notification::make()
                    ->title($result['errors'] > 0 ? 'Import Selesai dengan Error' : 'Import Berhasil')
                    ->body($msg)
                    ->success()
                    ->persistent()
                    ->send();

                $livewire->dispatch('refreshRelation');
            });
    }

    protected static function processFile(string $fullPath): array
    {
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $rows = ($ext === 'csv') ? self::readCsv($fullPath) : self::readXlsx($fullPath);

        if (empty($rows)) {
            throw new \RuntimeException('File kosong atau tidak valid.');
        }

        $headers = array_shift($rows);
        $headers = array_map(fn($h) => strtolower(trim((string) $h)), $headers);

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($rows as $rawRow) {
            $row = [];
            foreach ($headers as $i => $header) {
                $row[$header] = isset($rawRow[$i]) ? trim((string) $rawRow[$i]) : '';
            }

            // Mencari record
            $record = null;
            $id = $row['id'] ?? $row['id ljk'] ?? null;
            $idKrs = $row['id_akademik_krs'] ?? $row['id krs'] ?? null;
            $idMapelKelas = $row['id_mata_pelajaran_kelas'] ?? $row['id mapel kelas'] ?? null;

            if (filled($id)) {
                $record = SiswaDataLJK::find($id);
            } elseif (filled($idKrs) && filled($idMapelKelas)) {
                $record = SiswaDataLJK::where('id_akademik_krs', $idKrs)
                    ->where('id_mata_pelajaran_kelas', $idMapelKelas)
                    ->first();
            }

            if (!$record) {
                $skipped++;
                continue;
            }

            // Mapping data nilai
            $updateData = [];
            $fields = [
                'id' => 'id',
                'id ljk' => 'id',
                'id_akademik_krs' => 'id_akademik_krs',
                'id krs' => 'id_akademik_krs',
                'id_mata_pelajaran_kelas' => 'id_mata_pelajaran_kelas',
                'id mapel kelas' => 'id_mata_pelajaran_kelas',
                'nilai_uts' => 'Nilai_UTS',
                'nilai_uas' => 'Nilai_UAS',
                'nilai_performance' => 'Nilai_Performance',
                'perf' => 'Nilai_Performance',
                'performance' => 'Nilai_Performance',
                'uts' => 'Nilai_UTS',
                'uas' => 'Nilai_UAS',
            ];


            // Add TGS fields
            for ($i = 1; $i <= 12; $i++) {
                $fields["nilai_tgs_$i"] = "Nilai_TGS_$i";
                $fields["tgs_$i"] = "Nilai_TGS_$i";
                $fields["tgs $i"] = "Nilai_TGS_$i";
            }

            foreach ($fields as $csvHeader => $dbField) {
                if (array_key_exists($csvHeader, $row) && $row[$csvHeader] !== '') {
                    $updateData[$dbField] = (float) $row[$csvHeader];
                }
            }

            if (empty($updateData)) {
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($record, $updateData) {
                    $record->update($updateData);
                });
                $updated++;
            } catch (\Throwable $e) {
                Log::error("[ImportSiswaDataLjk] Row failed: " . $e->getMessage());
                $errors++;
            }
        }

        return [
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    protected static function readXlsx(string $path): array
    {
        $rows = [];
        $reader = new XlsxReader();
        try {
            $reader->open($path);
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCells();
                    $rowData = [];
                    foreach ($cells as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $rows[] = $rowData;
                }
                break;
            }
            $reader->close();
        } catch (\Throwable $e) {
            $reader->close();
            throw $e;
        }
        return $rows;
    }

    protected static function readCsv(string $path): array
    {
        $rows = [];
        $options = new CsvOptions();
        $reader = new CsvReader($options);
        try {
            $reader->open($path);
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCells();
                    $rowData = [];
                    foreach ($cells as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $rows[] = $rowData;
                }
                break;
            }
            $reader->close();
        } catch (\Throwable $e) {
            $reader->close();
            throw $e;
        }
        return $rows;
    }
}
