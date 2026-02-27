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
            ImportColumn::make('id_jurusan')
                ->label('Jurusan (Isi dengan ID)')
                ->guess(['jurusan', 'id_jurusan'])
                ->requiredMapping()
                ->rules(['required', 'integer'])
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
                ->examples(fn() => \App\Models\Jurusan::withoutGlobalScopes()->get()->map(function ($item) {
                    return $item->id . ' - ' . $item->nama;
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
        try {
            $kodeFeeder = isset($this->data['kode_feeder']) ? trim($this->data['kode_feeder']) : null;

            if (empty($kodeFeeder)) {
                throw new \Exception("Kolom 'kode_feeder' tidak boleh kosong.");
            }

            // Validasi id_jurusan wajib ada di data
            $idJurusan = $this->data['id_jurusan'] ?? null;
            if (empty($idJurusan)) {
                throw new \Exception("Kolom 'jurusan' (ID) tidak ditemukan atau kosong.");
            }

            // Cari record tanpa dipengaruhi Global Scope (Jenjang)
            $record = MataPelajaranMaster::withoutGlobalScopes()
                ->where('kode_feeder', $kodeFeeder)
                ->first();

            if ($record) {
                \Illuminate\Support\Facades\Log::info("Import: Ditemukan record existing (ID: {$record->id}) untuk kode {$kodeFeeder}. Melakukan update.");
                return $record;
            }

            \Illuminate\Support\Facades\Log::info("Import: Record baru untuk kode {$kodeFeeder}. Melakukan insert.");
            return new MataPelajaranMaster();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error pada baris import [Kode: " . ($kodeFeeder ?? 'N/A') . "]: " . $e->getMessage());
            // Lempar exception agar Filament mencatat baris ini sebagai 'failed'
            throw $e;
        }
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
