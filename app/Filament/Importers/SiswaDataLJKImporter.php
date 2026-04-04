<?php

namespace App\Filament\Importers;

use App\Models\SiswaDataLJK;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SiswaDataLJKImporter extends Importer
{
    protected static ?string $model = SiswaDataLJK::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->label('ID LJK')
                ->numeric()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('nama_mahasiswa')
                ->label('Nama Mahasiswa')
                ->ignore(true), // Just for reference in Excel
            ImportColumn::make('nim')
                ->label('NIM')
                ->ignore(true), // Just for reference in Excel
            ImportColumn::make('id_akademik_krs')
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('id_mata_pelajaran_kelas')
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('Nilai_UTS')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:4']),
            ImportColumn::make('Nilai_UAS')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:4']),
            ImportColumn::make('Nilai_TGS_1')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_2')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_3')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_4')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_5')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_6')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_7')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_8')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_9')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_10')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_11')
                ->numeric(),
            ImportColumn::make('Nilai_TGS_12')
                ->numeric(),
            ImportColumn::make('Nilai_Performance')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:4']),
        ];
    }

    public function resolveRecord(): ?SiswaDataLJK
    {
        if ($id = $this->data['id'] ?? null) {
            return SiswaDataLJK::find($id);
        }

        return SiswaDataLJK::firstOrNew([
            'id_akademik_krs' => $this->data['id_akademik_krs'],
            'id_mata_pelajaran_kelas' => $this->data['id_mata_pelajaran_kelas'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data nilai siswa telah selesai dan ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimpor.';
        }

        return $body;
    }
}
