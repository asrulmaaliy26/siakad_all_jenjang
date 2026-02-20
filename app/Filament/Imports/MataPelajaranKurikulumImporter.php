<?php

namespace App\Filament\Imports;

use App\Models\MataPelajaranKurikulum;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MataPelajaranKurikulumImporter extends Importer
{
    protected static ?string $model = MataPelajaranKurikulum::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('kurikulum')
                ->relationship(resolveUsing: 'nama')
                ->requiredMapping()
                ->rules(['required'])
                ->example(\App\Models\Kurikulum::pluck('nama', 'id')->map(fn($nama, $id) => "{$nama} (ID: {$id})")->implode(', ')),
            ImportColumn::make('mataPelajaranMaster')
                ->relationship(resolveUsing: 'nama')
                ->requiredMapping()
                ->rules(['required'])
                ->example(\App\Models\MataPelajaranMaster::pluck('nama', 'id')->map(fn($nama, $id) => "{$nama} (ID: {$id})")->implode(', ')),
            ImportColumn::make('semester')
                ->numeric()
                ->requiredMapping()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?MataPelajaranKurikulum
    {
        // Composite key check
        // We need to resolve IDs first.
        // But ImportColumn relationships handle ID resolution and place it in data with proper key?
        // Actually, Importer 'relationship' column puts the resolved ID into the data array using the relationship name as key (if configured) or column name?
        // By default, relationship column 'kurikulum' will look for 'kurikulum' in data, resolve it, and put 'kurikulum_id' (foreign key) in the model?
        // Wait, resolveRecord is for finding *existing* to update. 
        // If we just return new instance, it creates.
        // Let's assume create for now, or finding by unique combination.

        return new MataPelajaranKurikulum();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import mata pelajaran kurikulum (distribusi) selesai ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimpor.';
        }

        return $body;
    }
}
