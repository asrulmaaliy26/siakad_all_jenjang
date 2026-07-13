<?php

namespace App\Filament\Resources\SiswaDataLJKS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\MataPelajaranKelas;
use App\Models\SiswaDataLJK;

class SiswaDataLJKSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->toggleable(),

                TextColumn::make('id')
                    ->label('ID LJK')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('id_akademik_krs')
                    ->label('ID KRS')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('id_mata_pelajaran_kelas')
                    ->label('ID Mapel Kelas')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('akademikKrs.riwayatPendidikan.siswa.nama')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('akademikKrs.riwayatPendidikan.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('akademikKrs.riwayatPendidikan.programKelas.nilai')
                    ->label('Program Kelas')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('mataPelajaranKelas.kelas.tahunAkademik.nama')
                    ->label('Tahun Ajaran Kelas')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('mataPelajaranKelas.kelas.semester')
                    ->label('Semester Kelas')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state) => "Semester {$state}")
                    ->toggleable(),

                TextColumn::make('mapel_progress')
                    ->label('Mapel / Kelas')
                    ->badge()
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        // Ambil kelas dari mata pelajaran kelas record ini
                        $kelas = $record->mataPelajaranKelas?->kelas;
                        if (!$kelas) return '-';

                        $totalMapel = $kelas->mataPelajaranKelas()->count();
                        $krsId      = $record->id_akademik_krs;

                        $jumlahLjk = \App\Models\SiswaDataLJK::where('id_akademik_krs', $krsId)
                            ->whereHas('mataPelajaranKelas', fn($q) => $q->where('id_kelas', $kelas->id))
                            ->count();

                        return "{$jumlahLjk} / {$totalMapel} Mapel";
                    })
                    ->color(function ($record) {
                        $kelas = $record->mataPelajaranKelas?->kelas;
                        if (!$kelas) return 'gray';

                        $totalMapel = $kelas->mataPelajaranKelas()->count();
                        $krsId      = $record->id_akademik_krs;

                        $jumlahLjk = \App\Models\SiswaDataLJK::where('id_akademik_krs', $krsId)
                            ->whereHas('mataPelajaranKelas', fn($q) => $q->where('id_kelas', $kelas->id))
                            ->count();

                        return $jumlahLjk >= $totalMapel ? 'success' : 'warning';
                    }),

                TextColumn::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->toggleable(),

                TextColumn::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.bobot')
                    ->label('Bobot')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->toggleable(),

                TextColumn::make('mataPelajaranKelas.dosenData.nama')
                    ->label('Dosen')
                    ->sortable()
                    ->limit(20)
                    ->toggleable(),

                TextColumn::make('Nilai_UTS')->label('UTS')->sortable()->toggleable(),
                TextColumn::make('Nilai_UAS')->label('UAS')->sortable()->toggleable(),
                TextColumn::make('Rata_Tugas')
                    ->label('Tugas')
                    ->getStateUsing(function ($record) {
                        $sum = 0;
                        $count = 0;
                        for ($i = 1; $i <= 12; $i++) {
                            $val = $record->{"Nilai_TGS_{$i}"};
                            if ($val !== null && $val !== '') {
                                $sum += (float) $val;
                                $count++;
                            }
                        }
                        return $count > 0 ? round($sum / $count, 2) : 0;
                    })
                    ->toggleable(),
                TextColumn::make('Nilai_Performance')->label('Perf')->sortable()->toggleable(),

                TextColumn::make('Nilai_Akhir')
                    ->label('Akhir')
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),

                TextColumn::make('Nilai_Huruf')
                    ->label('Grade')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D', 'E' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('Status_Nilai')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => $state === 'LULUS' ? 'success' : 'danger')
                    ->toggleable(),

                TextColumn::make('ljk_uts')
                    ->label('LJK UTS')
                    ->formatStateUsing(fn($state) => $state ? 'Ada' : '-')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->url(function ($record) {
                        if (empty($record->ljk_uts)) return null;
                        $path = is_array($record->ljk_uts) ? ($record->ljk_uts[0] ?? null) : $record->ljk_uts;
                        return $path ? asset('storage/' . $path) : null;
                    })
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('ljk_uas')
                    ->label('LJK UAS')
                    ->formatStateUsing(fn($state) => $state ? 'Ada' : '-')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->url(function ($record) {
                        if (empty($record->ljk_uas)) return null;
                        $path = is_array($record->ljk_uas) ? ($record->ljk_uas[0] ?? null) : $record->ljk_uas;
                        return $path ? asset('storage/' . $path) : null;
                    })
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('cekal_kuliah')
                    ->label('Cekal')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'YA' : 'TDK')
                    ->color(fn($state) => $state === 'Y' ? 'danger' : 'success')
                    ->toggleable(),

                TextColumn::make('transfer')
                    ->label('Transfer')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'YA' : 'TDK')
                    ->color(fn($state) => $state === 'Y' ? 'info' : 'gray')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Input')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_akademik_krs')
                    ->label('Mahasiswa')
                    ->options(
                        \App\Models\AkademikKrs::with('riwayatPendidikan.siswa')
                            ->where('status_aktif', 'Y')
                            ->get()
                            ->mapWithKeys(fn($record) => [
                                $record->id => ($record->riwayatPendidikan?->siswa?->nama ?? '-') . ' (' . ($record->riwayatPendidikan?->nomor_induk ?? '-') . ')'
                            ])
                    )
                    ->searchable(),

                SelectFilter::make('id_mata_pelajaran_kelas')
                    ->label('Mata Pelajaran Kelas')
                    ->relationship('mataPelajaranKelas', 'id')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $matkul = optional($record->mataPelajaranKurikulum->mataPelajaranMaster)->nama ?? '-';
                        $kelas = optional($record->kelas->programKelas)->nilai ?? '-';
                        $dosen = optional($record->dosen)->nama ?? '-';
                        return "$matkul - $kelas ($dosen)";
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('dosen')
                    ->label('Dosen Pengajar')
                    ->relationship('mataPelajaranKelas.dosenData', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(
                        \App\Models\TahunAkademik::orderByDesc('id')
                            ->get()
                            ->pluck('nama', 'id')
                    )
                    ->default(\App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->query(function ($query, array $data) {
                        return $query->when($data['value'], function ($query, $value) {
                            $query->whereHas(
                                'mataPelajaranKelas.kelas',
                                fn($q) => $q->where('id_tahun_akademik', $value)
                            );
                        });
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('cekal_kuliah')
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswa.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')->heading('Mata Kuliah'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.dosenData.nama')->heading('Dosen Pengajar'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UTS')->heading('UTS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UAS')->heading('UAS'),
                                    ...\array_map(fn($i) => \pxlrbt\FilamentExcel\Columns\Column::make("Nilai_TGS_{$i}")->heading("TGS $i"), \range(1, 12)),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_Performance')->heading('Perf'),
                                ])
                        ]),
                    DeleteBulkAction::make()
                        ->disabled(function () {
                            /** @var \App\Models\User|null $user */
                            $user = auth()->user();
                            return $user && $user->isMurid();
                        }),
                ]),
            ])
            // ->toolbarActions([])
            ->headerActions([
                \Filament\Actions\Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()
                            ->with([
                                'akademikKrs.riwayatPendidikan.siswa',
                                'mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster',
                                'mataPelajaranKelas.dosenData'
                            ])
                            ->get();

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cetak.nilai-ljk', ['records' => $records])
                            ->setPaper('A4', 'landscape');

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            'Cetak_Data_Nilai_' . now()->format('Ymd_His') . '.pdf'
                        );
                    }),
                \Filament\Actions\Action::make('cetak_transkrip')
                    ->label('Cetak Transkrip')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn($livewire) => !empty($livewire->tableFilters['id_akademik_krs']['value']))
                    ->url(function ($livewire) {
                        $krsId = $livewire->tableFilters['id_akademik_krs']['value'];
                        $krs = \App\Models\AkademikKrs::find($krsId);
                        return $krs ? route('cetak.transkrip', $krs->riwayatPendidikan->id_siswa_data) : '#';
                    })
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('export_rekap_nilai')
                    ->label('Export Rekap Nilai (IPK/IPS)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (\Livewire\Component $livewire) {
                        $query = clone $livewire->getFilteredTableQuery();
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\RekapNilaiMahasiswaExport($query),
                            'Rekap-Nilai-' . date('Y-m-d-His') . '.xlsx'
                        );
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export Excel')
                    ->color('success')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswa.nama')->heading('Nama Mahasiswa'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.nomor_induk')->heading('NIM'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')->heading('Mata Kuliah'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.dosenData.nama')->heading('Dosen Pengajar'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UTS')->heading('UTS'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UAS')->heading('UAS'),
                                ...\array_map(fn($i) => \pxlrbt\FilamentExcel\Columns\Column::make("Nilai_TGS_{$i}")->heading("TGS $i"), \range(1, 12)),
                                \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_Performance')->heading('Perf'),
                            ])
                    ]),
                \Filament\Actions\Action::make('importExcelCustom')
                    ->label('Import Excel')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->storeFiles(false)
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $file = is_array($data['file']) ? $data['file'][0] : $data['file'];
                        $filePath = $file->getRealPath();
                        $import = new \App\Imports\SiswaDataLJKImport();
                        \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                        \Filament\Notifications\Notification::make()
                            ->title('Import Selesai')
                            ->body($import->successCount . ' baris berhasil diimpor.')
                            ->success()
                            ->send();
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                \Filament\Tables\Grouping\Group::make('akademikKrs.riwayatPendidikan.siswa.nama')
                    ->label('Mahasiswa')
                    ->collapsible(),
                \Filament\Tables\Grouping\Group::make('mataPelajaranKelas.kelas.tahunAkademik.nama')
                    ->label('Tahun Akademik Kelas')
                    ->collapsible(),
                \Filament\Tables\Grouping\Group::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Kuliah')
                    ->collapsible(),
            ])
            ->defaultGroup('akademikKrs.riwayatPendidikan.siswa.nama')
            ->paginated([10, 25, 50, 100, 250])
            ->defaultPaginationPageOption(25);
    }
}
