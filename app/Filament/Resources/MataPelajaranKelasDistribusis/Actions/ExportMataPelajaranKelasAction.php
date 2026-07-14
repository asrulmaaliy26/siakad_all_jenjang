<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Actions;

use App\Exports\MataPelajaranKelasExport;
use App\Models\MataPelajaranKelas;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
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
                $keyColumns     = array_filter(['id', 'kode_feeder'], fn($k) => in_array($k, $selectedColumns));
                $otherColumns   = array_filter($selectedColumns, fn($k) => !in_array($k, ['id', 'kode_feeder']));
                $orderedColumns = array_values(array_merge($keyColumns, $otherColumns));

                // Ambil data dari livewire — query sudah include filter & sort aktif
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

                try {
                    return Excel::download(
                        new MataPelajaranKelasExport($records, $orderedColumns),
                        $fileName,
                        $writerType
                    );
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Export gagal!')
                        ->body('[' . class_basename($e) . '] ' . $e->getMessage() . ' (baris ' . $e->getLine() . ')')
                        ->danger()
                        ->persistent()
                        ->send();

                    \Illuminate\Support\Facades\Log::error('Export MataPelajaranKelas gagal', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            });
    }


    /**
     * Buat bulk action export (untuk baris yang diseleksi).
     *
     * @param string $name Nama unik action
     */
    public static function makeBulk(string $name = 'export_selected_mapel_kelas'): Action
    {
        $allColumns      = MataPelajaranKelasExport::allColumns();
        $defaultSelected = array_keys($allColumns);

        return Action::make($name)
            ->label('Export Terpilih')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->bulk()
            ->accessSelectedRecords()  // ← WAJIB di Filament v4 agar bisa akses selected records
            ->modalHeading('Export Baris yang Dipilih')
            ->modalDescription('Kolom "ID" dan "Kode Feeder" disarankan selalu disertakan agar bisa diimport kembali.')
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
            ->action(function (array $data, Action $action, $livewire) {
                $selectedColumns = $data['columns_to_export'] ?? [];
                $fileFormat      = $data['file_format'] ?? 'xlsx';

                if (empty($selectedColumns)) {
                    Notification::make()
                        ->title('Pilih minimal satu kolom')
                        ->warning()
                        ->send();
                    return;
                }

                // Ambil ID dari selected records
                $selectedIds = $action->getSelectedRecords()->pluck('id');

                if ($selectedIds->isEmpty()) {
                    Notification::make()
                        ->title('Tidak ada baris yang dipilih')
                        ->body('Pilih setidaknya satu baris di tabel terlebih dahulu.')
                        ->warning()
                        ->send();
                    return;
                }

                // Ambil urutan sort aktif dari tabel melalui getFilteredSortedTableQuery,
                // lalu intersect dengan selectedIds agar urutan tabel terjaga
                $sortedIds = $livewire
                    ->getFilteredSortedTableQuery()
                    ->whereIn('mata_pelajaran_kelas.id', $selectedIds->toArray())
                    ->pluck('mata_pelajaran_kelas.id')
                    ->toArray();

                // If no search and no selected records, sort logic may be applied to all
                if (empty($sortedIds)) {
                    $records = MataPelajaranKelas::query()
                        ->with([
                            'mataPelajaranKurikulum.mataPelajaranMaster',
                            'kelas.jurusan',
                            'dosenData'
                        ])
                        ->get();
                } else {
                    // If there are sortedIds, fetch them in order
                    $records = MataPelajaranKelas::whereIn('id', $sortedIds)
                        ->with([
                            'mataPelajaranKurikulum.mataPelajaranMaster',
                            'kelas.programKelas',
                            'dosenData',
                            'ruangKelas',
                            'pelaksanaanKelas',
                        ])
                        ->orderByRaw('FIELD(id, ' . implode(',', $sortedIds) . ')')
                        ->get();
                }

                // Urutkan: kolom kunci di depan
                $keyColumns     = array_filter(['id', 'kode_feeder'], fn($k) => in_array($k, $selectedColumns));
                $otherColumns   = array_filter($selectedColumns, fn($k) => !in_array($k, ['id', 'kode_feeder']));
                $orderedColumns = array_values(array_merge($keyColumns, $otherColumns));

                $writerType = $fileFormat === 'csv'
                    ? \Maatwebsite\Excel\Excel::CSV
                    : \Maatwebsite\Excel\Excel::XLSX;

                $fileName = 'mata_pelajaran_kelas_selected_' . date('Ymd_His') . '.' . $fileFormat;

                try {
                    return Excel::download(
                        new MataPelajaranKelasExport($records, $orderedColumns),
                        $fileName,
                        $writerType
                    );
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Export Terpilih gagal!')
                        ->body('[' . class_basename($e) . '] ' . $e->getMessage() . ' (baris ' . $e->getLine() . ')')
                        ->danger()
                        ->persistent()
                        ->send();

                    \Illuminate\Support\Facades\Log::error('Export Terpilih MataPelajaranKelas gagal', [
                        'error'        => $e->getMessage(),
                        'selected_ids' => $selectedIds->toArray(),
                        'trace'        => $e->getTraceAsString(),
                    ]);
                }
            });
    }
}
