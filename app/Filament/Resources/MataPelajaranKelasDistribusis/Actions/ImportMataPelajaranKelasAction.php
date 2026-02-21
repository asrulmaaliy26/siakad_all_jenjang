<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Actions;

use App\Models\MataPelajaranKelasDistribusi;
use Carbon\Carbon;
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
 * Custom Import Action — memproses file Excel/CSV LANGSUNG (tanpa queue).
 * Cocok untuk environment tanpa queue worker.
 */
class ImportMataPelajaranKelasAction
{
    public static function make(string $name = 'import_mapel_kelas'): Action
    {
        return Action::make($name)
            ->label('Import / Update')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->modalHeading('Import & Update Mata Pelajaran Kelas')
            ->modalDescription(
                'Upload file Excel/CSV yang sudah diedit. ' .
                    'File harus memiliki kolom "id" atau "kode_feeder" sebagai kunci update. ' .
                    'Kolom yang kosong/blank akan dilewati (tidak menimpa data lama).'
            )
            ->modalSubmitActionLabel('Proses Import')
            ->form([
                FileUpload::make('import_file')
                    ->label('File Excel / CSV')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                        'application/csv',
                        'text/plain',
                    ])
                    ->disk('local')
                    ->directory('imports/mata-pelajaran-kelas')
                    ->required()
                    ->helperText('Format yang diterima: .xlsx atau .csv. Pastikan baris pertama adalah header.'),
            ])
            ->action(function (array $data, $livewire) {
                $filePath = $data['import_file'] ?? null;

                if (! $filePath) {
                    Notification::make()
                        ->title('File tidak ditemukan')
                        ->danger()
                        ->send();
                    return;
                }

                $fullPath = Storage::disk('local')->path($filePath);

                if (! file_exists($fullPath)) {
                    Notification::make()
                        ->title('File tidak bisa dibaca: ' . $fullPath)
                        ->danger()
                        ->send();
                    return;
                }

                // Baca file dan proses
                try {
                    $result = self::processFile($fullPath);
                } catch (\Throwable $e) {
                    Log::error('[ImportMataPelajaranKelas] Error membaca file: ' . $e->getMessage(), [
                        'file' => $fullPath,
                        'trace' => $e->getTraceAsString(),
                    ]);

                    Notification::make()
                        ->title('Gagal membaca file')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                    return;
                } finally {
                    // Hapus file temp setelah diproses
                    Storage::disk('local')->delete($filePath);
                }

                // Tampilkan hasil
                $msg = "✅ {$result['updated']} baris diperbarui.";
                if ($result['skipped'] > 0) {
                    $msg .= " ⏭️ {$result['skipped']} baris dilewati (ID/kode tidak ditemukan).";
                }
                if ($result['errors'] > 0) {
                    $msg .= " ❌ {$result['errors']} baris gagal (lihat log).";
                }
                if (! empty($result['error_details'])) {
                    $msg .= "\n\nDetail error:\n" . implode("\n", array_slice($result['error_details'], 0, 5));
                }

                $notif = Notification::make()
                    ->title($result['errors'] > 0 ? 'Import selesai dengan error' : 'Import berhasil')
                    ->body($msg)
                    ->persistent();

                if ($result['errors'] > 0) {
                    $notif->warning();
                } else {
                    $notif->success();
                }

                $notif->send();
            });
    }

    /**
     * Proses file Excel/CSV dan update database.
     *
     * @return array{updated: int, skipped: int, errors: int, error_details: string[]}
     */
    protected static function processFile(string $fullPath): array
    {
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            $rows = self::readCsv($fullPath);
        } else {
            $rows = self::readXlsx($fullPath);
        }

        if (empty($rows)) {
            throw new \RuntimeException('File kosong atau tidak memiliki data.');
        }

        // Baris pertama = header
        $headers = array_shift($rows);

        // Normalisasi header: lowercase + trim
        $headers = array_map(fn($h) => strtolower(trim((string) $h)), $headers);

        Log::info('[ImportMataPelajaranKelas] Header ditemukan: ' . implode(', ', $headers));

        $updated      = 0;
        $skipped      = 0;
        $errors       = 0;
        $errorDetails = [];
        $rowNum       = 1; // baris 1 = header, mulai dari baris 2

        foreach ($rows as $rawRow) {
            $rowNum++;

            // Gabungkan header dengan nilai baris
            $row = [];
            foreach ($headers as $i => $header) {
                $row[$header] = isset($rawRow[$i]) ? trim((string) $rawRow[$i]) : '';
            }

            // ── STEP 1: Cari record berdasarkan ID atau kode_feeder ──
            $record = null;
            $lookupKey = '';

            try {
                $id         = $row['id'] ?? '';
                $kodeFeeder = $row['kode_feeder'] ?? '';

                if (filled($id) && is_numeric($id) && (int) $id > 0) {
                    $record    = MataPelajaranKelasDistribusi::find((int) $id);
                    $lookupKey = "ID={$id}";
                }

                if (! $record && filled($kodeFeeder)) {
                    $record = MataPelajaranKelasDistribusi::whereHas(
                        'mataPelajaranKurikulum.mataPelajaranMaster',
                        fn($q) => $q->where('kode_feeder', $kodeFeeder)
                    )->first();
                    $lookupKey = "kode_feeder={$kodeFeeder}";
                }

                if (! $record) {
                    Log::warning("[ImportMataPelajaranKelas] Baris {$rowNum}: record tidak ditemukan. ID='{$id}', kode='{$kodeFeeder}'");
                    $skipped++;
                    continue;
                }
            } catch (\Throwable $e) {
                $msg = "Baris {$rowNum}: gagal mencari record — " . $e->getMessage();
                Log::error('[ImportMataPelajaranKelas] ' . $msg);
                $errorDetails[] = $msg;
                $errors++;
                continue;
            }

            // ── STEP 2: Siapkan data yang akan di-update ──
            $updateData = [];

            try {
                $fieldMap = [
                    'id_dosen_data'    => fn($v) => is_numeric($v) && $v > 0 ? (int) $v : null,
                    'jumlah'           => fn($v) => is_numeric($v) ? (int) $v : null,
                    'hari'             => fn($v) => $v,
                    'jam'              => fn($v) => $v,
                    'ruang_uts'        => fn($v) => $v,
                    'ruang_uas'        => fn($v) => $v,
                    'link_kelas'       => fn($v) => $v,
                    'passcode'         => fn($v) => $v,
                    'tanggal'          => function ($v) {
                        if (blank($v)) return null;
                        try {
                            return Carbon::parse($v)->format('Y-m-d');
                        } catch (\Throwable) {
                            return null;
                        }
                    },
                    'uts'              => function ($v) {
                        if (blank($v)) return null;
                        try {
                            return Carbon::parse($v)->format('Y-m-d H:i:s');
                        } catch (\Throwable) {
                            return null;
                        }
                    },
                    'uas'              => function ($v) {
                        if (blank($v)) return null;
                        try {
                            return Carbon::parse($v)->format('Y-m-d H:i:s');
                        } catch (\Throwable) {
                            return null;
                        }
                    },
                    'status_uts'       => fn($v) => in_array(strtoupper($v), ['Y', 'N']) ? strtoupper($v) : null,
                    'status_uas'       => fn($v) => in_array(strtoupper($v), ['Y', 'N']) ? strtoupper($v) : null,
                ];

                foreach ($fieldMap as $field => $cast) {
                    // Hanya update jika kolom ada di file DAN nilainya tidak kosong
                    if (! array_key_exists($field, $row)) {
                        continue;
                    }
                    $rawValue = $row[$field];
                    if ($rawValue === '' || $rawValue === null) {
                        continue; // blank → jangan timpa nilai lama
                    }
                    $castValue = $cast($rawValue);
                    if ($castValue !== null) {
                        $updateData[$field] = $castValue;
                    }
                }
            } catch (\Throwable $e) {
                $msg = "Baris {$rowNum} ({$lookupKey}): error menyiapkan data — " . $e->getMessage();
                Log::error('[ImportMataPelajaranKelas] ' . $msg);
                $errorDetails[] = $msg;
                $errors++;
                continue;
            }

            if (empty($updateData)) {
                Log::info("[ImportMataPelajaranKelas] Baris {$rowNum} ({$lookupKey}): tidak ada data yang berubah, dilewati.");
                $skipped++;
                continue;
            }

            // ── STEP 3: Simpan ke database dalam transaction ──
            try {
                DB::transaction(function () use ($record, $updateData) {
                    foreach ($updateData as $field => $value) {
                        $record->{$field} = $value;
                    }
                    $saved = $record->save();

                    if (! $saved) {
                        throw new \RuntimeException('Model->save() mengembalikan false.');
                    }
                });

                Log::info("[ImportMataPelajaranKelas] Baris {$rowNum} ({$lookupKey}): berhasil update.", $updateData);
                $updated++;
            } catch (\Throwable $e) {
                $msg = "Baris {$rowNum} ({$lookupKey}): gagal simpan — " . $e->getMessage();
                Log::error('[ImportMataPelajaranKelas] ' . $msg, [
                    'updateData' => $updateData,
                    'trace'      => $e->getTraceAsString(),
                ]);
                $errorDetails[] = $msg;
                $errors++;
            }
        }

        return [
            'updated'      => $updated,
            'skipped'      => $skipped,
            'errors'       => $errors,
            'error_details' => $errorDetails,
        ];
    }

    /**
     * Baca file XLSX menggunakan OpenSpout.
     */
    protected static function readXlsx(string $path): array
    {
        $rows   = [];
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
                break; // Hanya sheet pertama
            }

            $reader->close();
        } catch (\Throwable $e) {
            $reader->close();
            throw new \RuntimeException('Gagal membaca file XLSX: ' . $e->getMessage(), 0, $e);
        }

        return $rows;
    }

    /**
     * Baca file CSV menggunakan OpenSpout.
     */
    protected static function readCsv(string $path): array
    {
        $rows    = [];
        $options = new CsvOptions();
        $options->FIELD_DELIMITER = ',';
        $options->FIELD_ENCLOSURE = '"';
        $reader  = new CsvReader($options);

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
            throw new \RuntimeException('Gagal membaca file CSV: ' . $e->getMessage(), 0, $e);
        }

        return $rows;
    }
}
