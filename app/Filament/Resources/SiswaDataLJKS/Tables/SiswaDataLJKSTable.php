<?php

namespace App\Filament\Resources\SiswaDataLJKS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\MataPelajaranKelas;

class SiswaDataLJKSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
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
                ...array_map(fn($i) => TextColumn::make("Nilai_TGS_{$i}")->label("Tugas $i")->sortable()->toggleable(isToggledHiddenByDefault: $i > 3), range(1, 12)),
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
                    ->color(fn($state) => $state === 'Lulus' ? 'success' : 'danger')
                    ->toggleable(),

                TextColumn::make('ljk_uts')
                    ->label('LJK UTS')
                    ->formatStateUsing(fn($state) => $state ? 'Ada' : '-')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->url(fn($record) => $record->ljk_uts ? asset('storage/' . $record->ljk_uts) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('ljk_uas')
                    ->label('LJK UAS')
                    ->formatStateUsing(fn($state) => $state ? 'Ada' : '-')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->url(fn($record) => $record->ljk_uas ? asset('storage/' . $record->ljk_uas) : null)
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
                    ->options(\App\Models\TahunAkademik::all()->mapWithKeys(fn($t) => [$t->nama => "{$t->nama} - {$t->periode}"]))
                    ->default(\App\Models\TahunAkademik::where('status', 'Aktif')->first()?->nama)
                    ->query(function ($query, array $data) {
                        return $query->when($data['value'], function ($query, $value) {
                            $query->whereHas('akademikKrs', fn($q) => $q->where('kode_tahun', $value));
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
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
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
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export Excel')
                    ->color('success')
            ])
            ->defaultSort('created_at', 'desc');
    }
}
