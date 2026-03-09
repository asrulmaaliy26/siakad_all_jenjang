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
                ->ignoreBlankState()
                ->guess(['id_dosen_data', 'id dosen', 'id dosen (untuk update dosen)'])
                ->example('1'),

            ImportColumn::make('ro_ruang_kelas')
                ->label('ID Ruang Kelas (lihat sheet REF - Ruang Kelas)')
                ->rules(['nullable'])
                ->guess(['ro_ruang_kelas', 'id ruang kelas', 'ruang kelas id'])
                ->example('3')
                ->fillRecordUsing(function ($record, $state) {
                    // Tangani baik integer maupun string numerik
                    $val = is_numeric($state) ? (int) $state : null;
                    if ($val && $val > 0) {
                        $record->ro_ruang_kelas = $val;
                    }
                }),

            ImportColumn::make('ro_pelaksanaan_kelas')
                ->label('ID Pelaksanaan (lihat sheet REF - Pelaksanaan)')
                ->rules(['nullable'])
                ->guess(['ro_pelaksanaan_kelas', 'id pelaksanaan', 'pelaksanaan id'])
                ->example('1')
                ->fillRecordUsing(function ($record, $state) {
                    $val = is_numeric($state) ? (int) $state : null;
                    if ($val && $val > 0) {
                        $record->ro_pelaksanaan_kelas = $val;
                    }
                }),

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

    /**
     * Setelah record disimpan Filament, paksa update ro_ruang_kelas & ro_pelaksanaan_kelas
     * via DB langsung sebagai safety net — memastikan tersimpan meski fillRecordUsing gagal.
     */
    public function afterSave(): void
    {
        $updates = [];

        // $this->data berisi raw data dari baris Excel sebelum diproses
        $rawData = $this->data;

        \Illuminate\Support\Facades\Log::info('MataPelajaranKelasImport afterSave', [
            'record_id'            => $this->record?->id,
            'ro_ruang_kelas_raw'   => $rawData['ro_ruang_kelas'] ?? 'KEY_NOT_FOUND',
            'ro_pelaksanaan_raw'   => $rawData['ro_pelaksanaan_kelas'] ?? 'KEY_NOT_FOUND',
            'record_ro_ruang'      => $this->record?->ro_ruang_kelas,
            'record_ro_pelaksanaan' => $this->record?->ro_pelaksanaan_kelas,
            'all_keys'             => array_keys($rawData),
        ]);

        // Safety net: jika fillRecordUsing sudah bekerja, record sudah punya nilai
        // Jika belum, kita paksa update via DB
        $currentRuang      = $this->record?->getOriginal('ro_ruang_kelas');
        $currentPelaksanaan = $this->record?->getOriginal('ro_pelaksanaan_kelas');

        // Cek juga dari fresh record untuk pastikan
        if ($this->record?->id) {
            $fresh = \Illuminate\Support\Facades\DB::table('mata_pelajaran_kelas')
                ->where('id', $this->record->id)
                ->first(['ro_ruang_kelas', 'ro_pelaksanaan_kelas']);

            \Illuminate\Support\Facades\Log::info('DB fresh values', [
                'fresh_ro_ruang'       => $fresh?->ro_ruang_kelas,
                'fresh_ro_pelaksanaan' => $fresh?->ro_pelaksanaan_kelas,
            ]);
        }

        // Coba semua kemungkinan key name (dari header Excel yang berbeda)
        $ruangId = $rawData['ro_ruang_kelas']
            ?? $rawData['ro ruang kelas']
            ?? $rawData['id ruang kelas']
            ?? null;

        $pelaksanaanId = $rawData['ro_pelaksanaan_kelas']
            ?? $rawData['ro pelaksanaan kelas']
            ?? $rawData['id pelaksanaan']
            ?? null;

        if (is_numeric($ruangId) && (int) $ruangId > 0) {
            $updates['ro_ruang_kelas'] = (int) $ruangId;
        }

        if (is_numeric($pelaksanaanId) && (int) $pelaksanaanId > 0) {
            $updates['ro_pelaksanaan_kelas'] = (int) $pelaksanaanId;
        }

        if (!empty($updates) && $this->record?->id) {
            $affected = \Illuminate\Support\Facades\DB::table('mata_pelajaran_kelas')
                ->where('id', $this->record->id)
                ->update($updates);

            \Illuminate\Support\Facades\Log::info('afterSave DB update', [
                'record_id' => $this->record->id,
                'updates'   => $updates,
                'affected'  => $affected,
            ]);
        }
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
