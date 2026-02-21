<?php

namespace App\Filament\Imports;

use App\Models\MataPelajaranKelasDistribusi;
use App\Models\MataPelajaranKurikulum;
use App\Models\Kelas;
use App\Models\DosenData;
use App\Models\ProgramKelas;
use App\Models\TahunAkademik;
use App\Models\Jurusan;
use App\Models\Semester;
use App\Models\MataPelajaranMaster;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class MataPelajaranKelasDistribusiImporter extends Importer
{
    protected static ?string $model = MataPelajaranKelasDistribusi::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('mataPelajaranKurikulum')
                ->relationship(resolveUsing: 'id')
                ->requiredMapping()
                ->rules(['required', 'integer'])
                ->label('ID Mapel Kurikulum')
                ->example('1'),
            ImportColumn::make('kelas')
                ->relationship(resolveUsing: 'id')
                ->requiredMapping()
                ->rules(['required', 'integer'])
                ->label('ID Kelas')
                ->example('1'),
            ImportColumn::make('dosen')
                ->relationship(resolveUsing: 'id')
                ->rules(['integer', 'nullable'])
                ->label('ID Dosen')
                ->example('1'),
            ImportColumn::make('uts')
                ->rules(['datetime', 'nullable'])
                ->example('2025-10-10 08:00:00')
                ->label('Waktu UTS (YYYY-MM-DD HH:MM:SS)'),
            ImportColumn::make('uas')
                ->rules(['datetime', 'nullable'])
                ->example('2025-12-10 08:00:00')
                ->label('Waktu UAS (YYYY-MM-DD HH:MM:SS)'),
            ImportColumn::make('ro_ruang_kelas')
                ->numeric()
                ->rules(['integer', 'nullable'])
                ->label('ID Ruang Kelas'),
            ImportColumn::make('ro_pelaksanaan_kelas')
                ->numeric()
                ->rules(['integer', 'nullable'])
                ->label('ID Pelaksanaan Kelas'),
            ImportColumn::make('id_pengawas')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('jumlah')
                ->numeric()
                ->rules(['integer', 'nullable']),
            ImportColumn::make('hari')
                ->rules(['max:50', 'nullable'])
                ->example('Senin'),
            ImportColumn::make('tanggal')
                ->rules(['date', 'nullable'])
                ->example('2025-01-01'),
            ImportColumn::make('jam')
                ->rules(['max:50', 'nullable'])
                ->example('08:00-10:00'),
            ImportColumn::make('status_uts')
                ->label('Status UTS (Y/N)')
                ->rules(['nullable', 'in:Y,N'])
                ->example('Y')
                ->castStateUsing(fn($state) => strtoupper(trim($state)) === 'Y' ? 'Y' : 'N'),
            ImportColumn::make('status_uas')
                ->label('Status UAS (Y/N)')
                ->rules(['nullable', 'in:Y,N'])
                ->example('N')
                ->castStateUsing(fn($state) => strtoupper(trim($state)) === 'Y' ? 'Y' : 'N'),
            ImportColumn::make('ruang_uts')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('ruang_uas')
                ->rules(['max:100', 'nullable']),
            ImportColumn::make('link_kelas')
                ->rules(['nullable']),
            ImportColumn::make('passcode')
                ->rules(['max:100', 'nullable']),

            // === REFERENCE COLUMNS FOR CSV EXAMPLES ===

            // REF: Mapel Kurikulum dengan informasi lengkap (Nama Kurikulum & Semester)
            ImportColumn::make('ref_mapel_kurikulum')
                ->label('REF: Mapel Kurikulum (ID - Nama Mapel | Nama Kurikulum | Semester)')
                ->fillRecordUsing(fn() => null)
                ->examples(
                    fn() => MataPelajaranKurikulum::with(['mataPelajaranMaster', 'kurikulum'])
                        ->get()
                        ->map(function ($item) {
                            $namaMapel = $item->mataPelajaranMaster ? $item->mataPelajaranMaster->nama : 'Unknown';
                            $namaKurikulum = $item->kurikulum ? $item->kurikulum->nama : 'Unknown';
                            $semester = $item->semester ?? 'Unknown';

                            return sprintf(
                                '%d - %s | %s | Smt %s',
                                $item->id,
                                $namaMapel,
                                $namaKurikulum,
                                $semester
                            );
                        })
                        ->toArray()
                ),

            // REF: Kelas dengan informasi lengkap (Semester | Jurusan | Tahun Akademik | Status)
            ImportColumn::make('ref_kelas')
                ->label('REF: Kelas (ID - Semester | Jurusan | Tahun Akademik | Status)')
                ->fillRecordUsing(fn() => null)
                ->examples(
                    fn() => Kelas::with(['jurusan', 'tahunAkademik', 'programKelas'])
                        ->get()
                        ->map(function ($item) {
                            $semester = $item->semester ?? 'Unknown';
                            $namaJurusan = $item->jurusan ? $item->jurusan->nama : 'Unknown';
                            $tahunAkademik = $item->tahunAkademik ? $item->tahunAkademik->tahun : 'Unknown';
                            $status = $item->status_aktif ?? 'Aktif';
                            $nama = $item->programKelas ? $item->programKelas->nilai : 'Unknown';

                            return sprintf(
                                '%d - %s | Smt %s | %s | %s | %s',
                                $item->id,
                                $nama,
                                $semester,
                                $namaJurusan,
                                $tahunAkademik,
                                $status,
                            );
                        })
                        ->toArray()
                ),

            // REF: Dosen dengan informasi Jurusan
            ImportColumn::make('ref_dosen')
                ->label('REF: Dosen (ID - Nama | Jurusan)')
                ->fillRecordUsing(fn() => null)
                ->examples(
                    fn() => DosenData::with('jurusan')
                        ->select('id', 'nama', 'id_jurusan')
                        ->get()
                        ->map(function ($dosen) {
                            $namaJurusan = $dosen->jurusan ? $dosen->jurusan->nama : 'Tanpa Jurusan';

                            return sprintf(
                                '%d - %s | %s',
                                $dosen->id,
                                $dosen->nama,
                                $namaJurusan
                            );
                        })
                        ->toArray()
                ),
        ];
    }

    public function resolveRecord(): ?MataPelajaranKelasDistribusi
    {
        // Avoid undefined array key errors by checking existence
        if (isset($this->data['mataPelajaranKurikulum']) && isset($this->data['kelas'])) {
            return MataPelajaranKelasDistribusi::firstOrNew([
                'id_mata_pelajaran_kurikulum' => $this->data['mataPelajaranKurikulum'],
                'id_kelas' => $this->data['kelas'],
            ]);
        }

        return new MataPelajaranKelasDistribusi();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import distribusi mata pelajaran kelas selesai. ' . Number::format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimpor.';
        }

        return $body;
    }
}
