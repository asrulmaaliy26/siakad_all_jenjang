<?php

namespace App\Filament\Imports;

use App\Models\MataPelajaranMaster;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MataPelajaranMasterImporter extends Importer
{
    protected static ?string $model = MataPelajaranMaster::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('kode_feeder')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('KODE-001'),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(fn($state) => trim($state))
                ->example('Matematika'),
            ImportColumn::make('jurusan')
                ->relationship(resolveUsing: 'id')
                ->requiredMapping()
                ->rules(['required', 'integer'])
                ->label('Jurusan (Isi dengan ID)')
                ->example('1'),
            ImportColumn::make('bobot')
                ->numeric()
                ->rules(['integer'])
                ->example('3'),
            ImportColumn::make('jenis')
                ->label('Jenis (Ketik: wajib / peminatan)')
                ->guess(['jenis'])
                ->example('wajib')
                ->castStateUsing(fn($state) => match (strtolower($state)) {
                    'peminatan', 'pilihan' => 'peminatan',
                    default => 'wajib',
                }),

            // === Dummy Columns for Example CSV References ===
            ImportColumn::make('referensi_jurusan')
                ->label('Referensi Jurusan (ID - Nama)')
                ->fillRecordUsing(fn() => null) // Ignore when saving
                ->examples(fn() => \App\Models\Jurusan::with('jenjangPendidikan')->get()->map(function ($item) {
                    $jenjang = $item->jenjangPendidikan ? ' - ' . $item->jenjangPendidikan->nama : '';
                    return $item->id . ' - ' . $item->nama . $jenjang;
                })->toArray()),

            ImportColumn::make('referensi_jenis')
                ->label('Referensi Jenis (Ketik Salah Satu)')
                ->fillRecordUsing(fn() => null) // Ignore when saving
                ->examples([
                    'wajib',
                    'peminatan',
                ]),
        ];
    }


    public function resolveRecord(): ?MataPelajaranMaster
    {
        return MataPelajaranMaster::firstOrNew([
            // Use kode_feeder as unique key if available, else nama
            'kode_feeder' => $this->data['kode_feeder'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import mata pelajaran master selesai ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimpor.';
        }

        return $body;
    }
}
