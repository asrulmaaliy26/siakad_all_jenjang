<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Actions;

use App\Exports\MataPelajaranKelasExport;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Factory class untuk membuat Filament Action export MataPelajaranKelas.
 * Menggunakan pola factory (bukan extend Action) agar kompatibel dengan Filament v4.
 */
class ExportMataPelajaranKelasAction
{
    /**
     * Buat action export dengan pilihan kolom.
     *
     * @param string $name Nama unik action
     */
    public static function make(string $name = 'export_mapel_kelas'): Action
    {
        $allColumns = MataPelajaranKelasExport::allColumns();

        // Default: pilih semua kolom kecuali referensi read-only
        $defaultSelected = array_keys($allColumns);

        return Action::make($name)
            ->label('Export Data')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->modalHeading('Export Mata Pelajaran Kelas')
            ->modalDescription('Pilih kolom yang ingin diexport. Kolom "ID" dan "Kode Feeder" disarankan selalu disertakan agar file bisa diimport kembali.')
            ->modalSubmitActionLabel('Download')
            ->form([
                CheckboxList::make('columns_to_export')
                    ->label('Kolom yang akan diexport')
                    ->options($allColumns)
                    ->columns(2)
                    ->gridDirection('row')
                    ->bulkToggleable()
                    ->default($defaultSelected)
                    ->required()
                    ->helperText('💡 Selalu sertakan kolom ID / Kode Feeder agar hasil export bisa diimport kembali.'),

                Select::make('file_format')
                    ->label('Format File')
                    ->options([
                        'xlsx' => 'Excel (.xlsx) — Disarankan',
                        'csv'  => 'CSV (.csv)',
                    ])
                    ->default('xlsx')
                    ->required(),
            ])
            ->action(function (array $data, $livewire) {
                $selectedColumns = $data['columns_to_export'] ?? [];
                $fileFormat      = $data['file_format'] ?? 'xlsx';

                if (empty($selectedColumns)) {
                    Notification::make()
                        ->title('Pilih minimal satu kolom')
                        ->warning()
                        ->send();
                    return;
                }

                // Pastikan kolom kunci selalu ada di awal
                $keyColumns = array_filter(['id', 'kode_feeder'], fn($k) => in_array($k, $selectedColumns));
                $otherColumns = array_filter($selectedColumns, fn($k) => !in_array($k, ['id', 'kode_feeder']));
                $orderedColumns = array_values(array_merge($keyColumns, $otherColumns));

                // Ambil data dari livewire (query dengan filter & sort aktif)
                $query   = $livewire->getFilteredSortedTableQuery();
                $records = $query->with([
                    'mataPelajaranKurikulum.mataPelajaranMaster',
                    'kelas.programKelas',
                    'dosen',
                    'ruangKelas',
                    'pelaksanaanKelas',
                ])->get();

                if ($records->isEmpty()) {
                    Notification::make()
                        ->title('Tidak ada data untuk diexport')
                        ->body('Pastikan filter tidak terlalu ketat.')
                        ->warning()
                        ->send();
                    return;
                }

                $writerType = $fileFormat === 'csv'
                    ? \Maatwebsite\Excel\Excel::CSV
                    : \Maatwebsite\Excel\Excel::XLSX;

                $fileName = 'mata_pelajaran_kelas_' . date('Ymd_His') . '.' . $fileFormat;

                return Excel::download(
                    new MataPelajaranKelasExport($records, $orderedColumns),
                    $fileName,
                    $writerType
                );
            });
    }

    /**
     * Buat bulk action export (untuk baris yang diseleksi).
     *
     * @param string $name Nama unik action
     */
    public static function makeBulk(string $name = 'export_selected_mapel_kelas'): Action
    {
        $allColumns = MataPelajaranKelasExport::allColumns();
        $defaultSelected = array_keys($allColumns);

        return Action::make($name)
            ->label('Export Terpilih')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->bulk()
            ->modalHeading('Export Baris yang Dipilih')
            ->modalDescription('Kolom "ID" dan "Kode Feeder" disarankan selalu disertakan.')
            ->modalSubmitActionLabel('Download')
            ->form([
                CheckboxList::make('columns_to_export')
                    ->label('Kolom yang akan diexport')
                    ->options($allColumns)
                    ->columns(2)
                    ->gridDirection('row')
                    ->bulkToggleable()
                    ->default($defaultSelected)
                    ->required()
                    ->helperText('💡 Selalu sertakan kolom ID / Kode Feeder agar hasil export bisa diimport kembali.'),

                Select::make('file_format')
                    ->label('Format File')
                    ->options([
                        'xlsx' => 'Excel (.xlsx) — Disarankan',
                        'csv'  => 'CSV (.csv)',
                    ])
                    ->default('xlsx')
                    ->required(),
            ])
            ->action(function (array $data, $livewire) {
                $selectedColumns = $data['columns_to_export'] ?? [];
                $fileFormat      = $data['file_format'] ?? 'xlsx';

                if (empty($selectedColumns)) {
                    Notification::make()
                        ->title('Pilih minimal satu kolom')
                        ->warning()
                        ->send();
                    return;
                }

                // Urutkan: kolom kunci di depan
                $keyColumns   = array_filter(['id', 'kode_feeder'], fn($k) => in_array($k, $selectedColumns));
                $otherColumns = array_filter($selectedColumns, fn($k) => !in_array($k, ['id', 'kode_feeder']));
                $orderedColumns = array_values(array_merge($keyColumns, $otherColumns));

                // Ambil baris yang diseleksi
                $records = null;
                try {
                    $selected = $livewire->getSelectedTableRecords();
                    if ($selected && $selected->isNotEmpty()) {
                        $records = $selected->load([
                            'mataPelajaranKurikulum.mataPelajaranMaster',
                            'kelas.programKelas',
                            'dosen',
                            'ruangKelas',
                            'pelaksanaanKelas',
                        ]);
                    }
                } catch (\Throwable) {
                    // Fallback ke semua data jika seleksi tidak tersedia
                }

                if (! $records || $records->isEmpty()) {
                    Notification::make()
                        ->title('Tidak ada baris yang dipilih')
                        ->body('Silakan pilih setidaknya satu baris sebelum export.')
                        ->warning()
                        ->send();
                    return;
                }

                $writerType = $fileFormat === 'csv'
                    ? \Maatwebsite\Excel\Excel::CSV
                    : \Maatwebsite\Excel\Excel::XLSX;

                $fileName = 'mata_pelajaran_kelas_selected_' . date('Ymd_His') . '.' . $fileFormat;

                return Excel::download(
                    new MataPelajaranKelasExport($records, $orderedColumns),
                    $fileName,
                    $writerType
                );
            });
    }
}
