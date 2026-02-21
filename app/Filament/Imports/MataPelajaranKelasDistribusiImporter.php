<?php

namespace App\Filament\Imports;

use App\Models\MataPelajaranKelasDistribusi;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Carbon\Carbon;

class MataPelajaranKelasDistribusiImporter extends Importer
{
    protected static ?string $model = MataPelajaranKelasDistribusi::class;

    public static function getColumns(): array
    {
        return [
            // ═══════════════════════════════════════════════════════════════
            // KUNCI IDENTIFIKASI — salah satu wajib di-map agar bisa update
            // ═══════════════════════════════════════════════════════════════
            ImportColumn::make('id')
                ->label('ID Mata Pelajaran Kelas')
                ->numeric()
                ->integer()
                ->rules(['nullable', 'integer'])
                ->fillRecordUsing(fn() => null)  // tidak di-set ke model, hanya dipakai resolveRecord
                ->guess(['id', 'id mata pelajaran kelas', 'ID', 'ID Mata Pelajaran Kelas'])
                ->example('1'),

            ImportColumn::make('kode_feeder')
                ->label('Kode Feeder (Kunci Import)')
                ->rules(['nullable', 'string', 'max:255'])
                ->fillRecordUsing(fn() => null)  // tidak di-set ke model, hanya dipakai resolveRecord
                ->guess(['kode_feeder', 'kode feeder', 'kode feeder (kunci import)', 'kodefeeder'])
                ->example('MPK-001'),

            // ═══════════════════════════════════════════════════════════════
            // KOLOM DATA — semua bisa diupdate secara independent
            // ═══════════════════════════════════════════════════════════════
            ImportColumn::make('id_dosen_data')
                ->label('ID Dosen')
                ->numeric()
                ->integer()
                ->rules(['nullable', 'integer'])
                ->ignoreBlankState()  // jika kosong, tidak menimpa data existing
                ->guess(['id_dosen_data', 'id dosen', 'id dosen (untuk update dosen)'])
                ->example('1'),

            ImportColumn::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->integer()
                ->rules(['nullable', 'integer'])
                ->ignoreBlankState()
                ->guess(['jumlah'])
                ->example('30'),

            ImportColumn::make('hari')
                ->label('Hari')
                ->rules(['nullable', 'string', 'max:50'])
                ->ignoreBlankState()  // jika kosong, tidak menimpa data existing
                ->guess(['hari'])
                ->example('Senin'),

            ImportColumn::make('tanggal')
                ->label('Tanggal (YYYY-MM-DD)')
                ->rules(['nullable', 'date'])
                ->ignoreBlankState()
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return Carbon::parse($state)->format('Y-m-d');
                    } catch (\Throwable) {
                        return null;
                    }
                })
                ->guess(['tanggal', 'tanggal (yyyy-mm-dd)'])
                ->example('2025-08-25'),

            ImportColumn::make('jam')
                ->label('Jam')
                ->rules(['nullable', 'string', 'max:50'])
                ->ignoreBlankState()
                ->guess(['jam'])
                ->example('08:00-10:00'),

            ImportColumn::make('uts')
                ->label('Jadwal UTS (YYYY-MM-DD HH:MM:SS)')
                ->rules(['nullable', 'date'])
                ->ignoreBlankState()
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return Carbon::parse($state)->format('Y-m-d H:i:s');
                    } catch (\Throwable) {
                        return null;
                    }
                })
                ->guess(['uts', 'jadwal uts', 'jadwal uts (yyyy-mm-dd hh:mm:ss)'])
                ->example('2025-10-10 08:00:00'),

            ImportColumn::make('uas')
                ->label('Jadwal UAS (YYYY-MM-DD HH:MM:SS)')
                ->rules(['nullable', 'date'])
                ->ignoreBlankState()
                ->castStateUsing(function ($state) {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return Carbon::parse($state)->format('Y-m-d H:i:s');
                    } catch (\Throwable) {
                        return null;
                    }
                })
                ->guess(['uas', 'jadwal uas', 'jadwal uas (yyyy-mm-dd hh:mm:ss)'])
                ->example('2025-12-10 08:00:00'),

            ImportColumn::make('status_uts')
                ->label('Status UTS (Y / N)')
                ->rules(['nullable', 'in:Y,N,y,n'])
                ->ignoreBlankState()
                ->castStateUsing(fn($state) => filled($state) ? strtoupper(trim($state)) : null)
                ->guess(['status_uts', 'status uts', 'status uts (y / n)'])
                ->example('Y'),

            ImportColumn::make('status_uas')
                ->label('Status UAS (Y / N)')
                ->rules(['nullable', 'in:Y,N,y,n'])
                ->ignoreBlankState()
                ->castStateUsing(fn($state) => filled($state) ? strtoupper(trim($state)) : null)
                ->guess(['status_uas', 'status uas', 'status uas (y / n)'])
                ->example('N'),

            ImportColumn::make('ruang_uts')
                ->label('Ruang UTS')
                ->rules(['nullable', 'string', 'max:100'])
                ->ignoreBlankState()
                ->guess(['ruang_uts', 'ruang uts'])
                ->example('Aula A'),

            ImportColumn::make('ruang_uas')
                ->label('Ruang UAS')
                ->rules(['nullable', 'string', 'max:100'])
                ->ignoreBlankState()
                ->guess(['ruang_uas', 'ruang uas'])
                ->example('Aula B'),

            ImportColumn::make('link_kelas')
                ->label('Link Kelas')
                ->rules(['nullable', 'string', 'max:500'])
                ->ignoreBlankState()
                ->guess(['link_kelas', 'link kelas'])
                ->example('https://meet.google.com/xxx'),

            ImportColumn::make('passcode')
                ->label('Passcode')
                ->rules(['nullable', 'string', 'max:100'])
                ->ignoreBlankState()
                ->guess(['passcode'])
                ->example('abc123'),

            // ═══════════════════════════════════════════════════════════════
            // KOLOM REFERENSI — hanya untuk informasi, tidak disimpan
            // ═══════════════════════════════════════════════════════════════
            ImportColumn::make('mata_pelajaran')
                ->label('Nama Mata Pelajaran [REF - tidak diimport]')
                ->fillRecordUsing(fn() => null)
                ->guess(['mata_pelajaran', 'nama mata pelajaran'])
                ->example('Matematika Dasar'),

            ImportColumn::make('program_kelas')
                ->label('Program Kelas [REF - tidak diimport]')
                ->fillRecordUsing(fn() => null)
                ->guess(['program_kelas', 'program kelas'])
                ->example('Reguler Pagi'),

            ImportColumn::make('dosen_nama')
                ->label('Nama Dosen [REF - tidak diimport]')
                ->fillRecordUsing(fn() => null)
                ->guess(['dosen_nama', 'nama dosen'])
                ->example('Dr. Budi'),

            ImportColumn::make('ruang')
                ->label('Ruang Kelas [REF - tidak diimport]')
                ->fillRecordUsing(fn() => null)
                ->guess(['ruang', 'ruang kelas'])
                ->example('Kelas A101'),

            ImportColumn::make('pelaksanaan')
                ->label('Pelaksanaan [REF - tidak diimport]')
                ->fillRecordUsing(fn() => null)
                ->guess(['pelaksanaan'])
                ->example('Online'),
        ];
    }

    /**
     * Resolve record untuk di-update.
     *
     * Prioritas lookup:
     *   1. ID langsung   → paling akurat
     *   2. kode_feeder   → lewat relasi mataPelajaranKurikulum.mataPelajaranMaster
     *
     * Mengembalikan null jika tidak ditemukan (baris diabaikan, tidak membuat record baru).
     */
    public function resolveRecord(): ?MataPelajaranKelasDistribusi
    {
        $id         = $this->data['id'] ?? null;
        $kodeFeeder = $this->data['kode_feeder'] ?? null;

        // ── Prioritas 1: cari by primary key ──
        if (filled($id) && (int) $id > 0) {
            $record = MataPelajaranKelasDistribusi::find((int) $id);
            if ($record) {
                return $record;
            }
        }

        // ── Prioritas 2: cari by kode_feeder ──
        if (filled($kodeFeeder)) {
            $record = MataPelajaranKelasDistribusi::whereHas(
                'mataPelajaranKurikulum.mataPelajaranMaster',
                fn($q) => $q->where('kode_feeder', trim($kodeFeeder))
            )->first();

            if ($record) {
                return $record;
            }
        }

        // Tidak ditemukan → skip baris ini (jangan buat record baru)
        return null;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import / Update Mata Pelajaran Kelas selesai. '
            . number_format($import->successful_rows)
            . ' baris berhasil diperbarui.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal (cek file detail).';
        }

        return $body;
    }
}
