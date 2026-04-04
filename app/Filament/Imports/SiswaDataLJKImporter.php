<?php

namespace App\Filament\Imports;

use App\Models\SiswaDataLJK;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SiswaDataLJKImporter extends Importer
{
    protected static ?string $model = SiswaDataLJK::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id_akademik_krs')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('id_mata_pelajaran_kelas')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('nilai')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_simulasi')
                ->rules(['max:255']),
            ImportColumn::make('ljk_uas'),
            ImportColumn::make('artikel_uas'),
            ImportColumn::make('tgl_upload_ljk_uas')
                ->rules(['max:50']),
            ImportColumn::make('tgl_upload_artikel_uas')
                ->rules(['max:50']),
            ImportColumn::make('ljk_uts'),
            ImportColumn::make('artikel_uts'),
            ImportColumn::make('tgl_upload_ljk_uts')
                ->rules(['max:50']),
            ImportColumn::make('tgl_upload_artikel_uts')
                ->rules(['max:50']),
            ImportColumn::make('tugas'),
            ImportColumn::make('ljk_tugas_1'),
            ImportColumn::make('ljk_tugas_2'),
            ImportColumn::make('ljk_tugas_3'),
            ImportColumn::make('tgl_upload_tugas')
                ->rules(['max:50']),
            ImportColumn::make('Nilai_UTS')
                ->rules(['max:255']),
            ImportColumn::make('Nilai_TGS_1')
                ->rules(['max:255']),
            ImportColumn::make('Nilai_TGS_2')
                ->rules(['max:255']),
            ImportColumn::make('Nilai_TGS_3')
                ->rules(['max:255']),
            ImportColumn::make('Nilai_UAS')
                ->rules(['max:255']),
            ImportColumn::make('Nilai_Performance')
                ->rules(['max:255']),
            ImportColumn::make('Nilai_Akhir')
                ->rules(['max:255']),
            ImportColumn::make('Nilai_Huruf')
                ->rules(['max:255']),
            ImportColumn::make('Status_Nilai')
                ->rules(['max:20']),
            ImportColumn::make('Rekom_Nilai')
                ->rules(['max:255']),
            ImportColumn::make('ket'),
            ImportColumn::make('transfer'),
            ImportColumn::make('cekal_kuliah'),
            ImportColumn::make('ctt_uts'),
            ImportColumn::make('ctt_uas'),
            ImportColumn::make('ctt_tugas_1'),
            ImportColumn::make('ctt_tugas_2'),
            ImportColumn::make('ctt_tugas_3'),
            ImportColumn::make('ljk_tugas_4'),
            ImportColumn::make('ctt_tugas_4'),
            ImportColumn::make('Nilai_TGS_4')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_5'),
            ImportColumn::make('ctt_tugas_5'),
            ImportColumn::make('Nilai_TGS_5')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_6'),
            ImportColumn::make('ctt_tugas_6'),
            ImportColumn::make('Nilai_TGS_6')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_7'),
            ImportColumn::make('ctt_tugas_7'),
            ImportColumn::make('Nilai_TGS_7')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_8'),
            ImportColumn::make('ctt_tugas_8'),
            ImportColumn::make('Nilai_TGS_8')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_9'),
            ImportColumn::make('ctt_tugas_9'),
            ImportColumn::make('Nilai_TGS_9')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_10'),
            ImportColumn::make('ctt_tugas_10'),
            ImportColumn::make('Nilai_TGS_10')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_11'),
            ImportColumn::make('ctt_tugas_11'),
            ImportColumn::make('Nilai_TGS_11')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('ljk_tugas_12'),
            ImportColumn::make('ctt_tugas_12'),
            ImportColumn::make('Nilai_TGS_12')
                ->numeric()
                ->rules(['integer']),
        ];
    }

    public function resolveRecord(): SiswaDataLJK
    {
        return new SiswaDataLJK();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your siswa data l j k import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
