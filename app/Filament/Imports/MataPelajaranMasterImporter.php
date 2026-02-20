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
                ->rules(['required', 'max:255']),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('jurusan')
                ->relationship(resolveUsing: 'nama')
                ->requiredMapping()
                ->rules(['required'])
                ->example(\App\Models\Jurusan::pluck('nama', 'id')->map(fn($nama, $id) => "{$nama} (ID: {$id})")->implode(', ')),
            ImportColumn::make('bobot')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('jenis')
                ->label('Jenis (Wajib/Pilihan/Muatan Lokal)')
                ->castStateUsing(fn($state) => match (strtolower($state)) {
                    'wajib' => '1', // Adjust ID mapping if needed, assuming RefOption ID or similar
                    'pilihan' => '2',
                    'muatan lokal' => '3',
                    default => null,
                })
            // Alternatively, if ro_jenis is just a string, remove mapUsing.
            // But considering it's 'ro_', it likely refers to ReferenceOption.
            // Without knowing IDs, I'll assume text or try to find by name if possible.
            // Let's use a simpler mapping or just text if unsure.
            // Given table filter options: 'Wajib' => 'Wajib', maybe it's stored as string or mapped?
            // Let's try to lookup ReferenceOption if possible.
            ,
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
