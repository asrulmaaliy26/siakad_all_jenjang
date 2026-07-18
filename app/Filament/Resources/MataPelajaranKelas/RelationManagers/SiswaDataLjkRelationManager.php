<?php

namespace App\Filament\Resources\MataPelajaranKelas\RelationManagers;

use App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource;
use App\Models\AkademikKRS;
use App\Models\SiswaDataLJK;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SelectColumn;
use App\Filament\Resources\MataPelajaranKelas\Actions\ImportSiswaDataLjkAction;
use Barryvdh\DomPDF\Facade\Pdf;


class SiswaDataLjkRelationManager extends RelationManager
{
    protected static string $relationship = 'siswaDataLjk';

    protected static ?string $title = 'Data LJK / Nilai';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('id')
                    ->label('ID LJK')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('id_akademik_krs')
                    ->label('ID KRS')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('id_mata_pelajaran_kelas')
                    ->label('ID Mapel Kelas')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->hidden(fn($livewire) => $livewire->getOwnerRecord() instanceof \App\Models\MataPelajaranKelas),
                TextColumn::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.bobot')
                    ->label('SKS')
                    ->badge()
                    ->color('info')
                    ->hidden(fn($livewire) => $livewire->getOwnerRecord() instanceof \App\Models\MataPelajaranKelas),
                TextColumn::make('mataPelajaranKelas.dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn($livewire) => $livewire->getOwnerRecord() instanceof \App\Models\MataPelajaranKelas),
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn($livewire) => auth()->user()?->isMurid() || $livewire->getOwnerRecord() instanceof \App\Models\AkademikKrs),
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn($livewire) => auth()->user()?->isMurid() || $livewire->getOwnerRecord() instanceof \App\Models\AkademikKrs),
                TextInputColumn::make('Nilai_UTS')
                    ->label('UTS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
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
                    ->color(fn() => auth()->user()?->isMurid() ? null : 'primary')
                    ->weight(fn() => auth()->user()?->isMurid() ? null : 'bold')
                    ->icon(fn() => auth()->user()?->isMurid() ? null : 'heroicon-o-pencil-square')
                    ->action(
                        \Filament\Actions\Action::make('input_tugas')
                            ->modalHeading(fn($record) => 'Input Nilai Tugas - ' . ($record->akademikKrs->riwayatPendidikan->siswaData->nama ?? 'Siswa'))
                            ->form(function () {
                                $inputs = [];
                                for ($i = 1; $i <= 12; $i++) {
                                    $inputs[] = \Filament\Forms\Components\TextInput::make("Nilai_TGS_{$i}")
                                        ->label("Tugas $i")
                                        ->numeric()
                                        ->step(0.01)
                                        ->minValue(0)
                                        ->maxValue(4);
                                }
                                return [
                                    \Filament\Schemas\Components\Grid::make(3)->schema($inputs)
                                ];
                            })
                            ->fillForm(function ($record) {
                                $data = [];
                                for ($i = 1; $i <= 12; $i++) {
                                    $data["Nilai_TGS_{$i}"] = $record->{"Nilai_TGS_{$i}"};
                                }
                                return $data;
                            })
                            ->action(function ($record, array $data) {
                                $record->update($data);
                            })
                            ->disabled(fn() => auth()->user()?->isMurid())
                    )
                    ->toggleable(),
                TextInputColumn::make('Nilai_UAS')
                    ->label('UAS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_Performance')
                    ->label('Perf')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextColumn::make('Nilai_Akhir')
                    ->label('Akhir')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('Nilai_Huruf')
                    ->label('Grade')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'A', 'A-' => 'success',
                        'B+', 'B', 'B-' => 'info',
                        'C+', 'C', 'C-' => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                SelectColumn::make('Status_Nilai')
                    ->label('Status')
                    ->options([
                        'LULUS' => 'LULUS',
                        'TL' => 'TIDAK LULUS',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->disabled(fn() => auth()->user() && auth()->user()->isMurid()),
            ])
            ->filters([
                //
            ])
            ->columnToggleFormColumns(3)
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                if ($user && $user->isMurid()) {
                    // Murid hanya melihat data LJK/nilai miliknya sendiri
                    $query->whereHas('akademikKrs.riwayatPendidikan.siswaData', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            })
            ->headerActions([
                Action::make('sync_students')
                    ->label('Sync Mahasiswa')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn($livewire) => ! auth()->user()?->isMurid() && $livewire->getOwnerRecord() instanceof \App\Models\MataPelajaranKelas)
                    ->action(function () {
                        $record = $this->getOwnerRecord();
                        $krsList = \App\Models\AkademikKrs::where('id_kelas', $record->id_kelas)->get();

                        foreach ($krsList as $krs) {
                            $ljk = \App\Models\SiswaDataLJK::firstOrCreate([
                                'id_mata_pelajaran_kelas' => $record->id,
                                'id_akademik_krs'         => $krs->id,
                            ], [
                                'nilai' => 0,
                            ]);

                            // Trigger save to force recalculation (booted event will run)
                            $ljk->save();
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Data berhasil disinkronisasi & direfresh')
                            ->success()
                            ->send();
                    }),
                ActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                        ->label('Export Excel')
                        ->color('success')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')->heading('Mata Kuliah'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.dosenData.nama')->heading('Dosen Pengajar'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UTS')->heading('UTS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UAS')->heading('UAS'),
                                    ...\array_map(fn($i) => \pxlrbt\FilamentExcel\Columns\Column::make("Nilai_TGS_{$i}")->heading("TGS $i"), \range(1, 12)),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_Performance')->heading('Perf'),
                                ])
                                ->withFilename(function () {
                                    $record = $this->getOwnerRecord();
                                    if ($record instanceof \App\Models\AkademikKrs) {
                                        return 'Export_Nilai_AKM_' . str($record->riwayatPendidikan->nomor_induk)->slug('_') . '_' . now()->format('YmdHis');
                                    }
                                    return 'Export_Nilai_' . str($record->mataPelajaranKurikulum->mataPelajaranMaster->nama)->slug('_') . '_' . now()->format('YmdHis');
                                }),
                        ]),
                    ImportSiswaDataLjkAction::make()
                        ->visible(fn($livewire) => ! auth()->user()?->isMurid() && $livewire->getOwnerRecord() instanceof \App\Models\MataPelajaranKelas),
                    Action::make('sync_semua_nilai')
                        ->label('Sync Seluruh Nilai')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Sinkronisasi Ulang Seluruh Nilai')
                        ->modalDescription('Apakah Anda yakin ingin menghitung ulang nilai untuk SELURUH mahasiswa pada kelas ini? Ini akan memperbarui Grade dan Status kelulusan sesuai dengan rumus terbaru.')
                        ->visible(fn($livewire) => ! auth()->user()?->isMurid())
                        ->action(function ($livewire) {
                            $records = $livewire->getFilteredTableQuery()->get();
                            foreach ($records as $record) {
                                $record->Status_Nilai = null;
                                $record->save();
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil menghitung ulang nilai untuk seluruh mahasiswa di kelas ini.')
                                ->success()
                                ->send();
                        }),
                    Action::make('cetak_pdf')
                        ->label('Cetak PDF Resmi')
                        ->icon('heroicon-o-printer')
                        ->color('danger')
                        ->visible(fn($livewire) => ! auth()->user()?->isMurid() && $livewire->getOwnerRecord() instanceof \App\Models\MataPelajaranKelas)
                        ->action(function ($livewire) {
                            $record = $this->getOwnerRecord();
                            $record->load([
                                'kelas.jurusan.fakultas',
                                'kelas.tahunAkademik',
                                'kelas.programKelas',
                                'dosenData',
                                'mataPelajaranKurikulum.mataPelajaranMaster'
                            ]);

                            $records = $livewire->getFilteredTableQuery()
                                ->with([
                                    'akademikKrs.riwayatPendidikan.siswa',
                                ])
                                ->get();

                            $pdf = Pdf::loadView('cetak.nilai-dpna', [
                                'kelas' => $record,
                                'records' => $records,
                            ])->setPaper('a4', 'portrait');

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'DPNA_' . str($record->mataPelajaranKurikulum->mataPelajaranMaster->nama)->slug('_') . '_' . now()->format('YmdHis') . '.pdf'
                            );
                        }),

                ])
                    ->label('Opsi Excel')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button(),
            ])
            ->actions([
                DeleteAction::make()
                    ->visible(fn() => ! auth()->user()?->isMurid()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')->heading('Mata Kuliah'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.dosenData.nama')->heading('Dosen Pengajar'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UTS')->heading('UTS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UAS')->heading('UAS'),
                                    ...\array_map(fn($i) => \pxlrbt\FilamentExcel\Columns\Column::make("Nilai_TGS_{$i}")->heading("TGS $i"), \range(1, 12)),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_Performance')->heading('Perf'),
                                ])
                                ->withFilename(function () {
                                    $record = $this->getOwnerRecord();
                                    return 'Bulk_Export_Nilai_' . str($record->mataPelajaranKurikulum->mataPelajaranMaster->nama)->slug('_') . '_' . now()->format('YmdHis');
                                }),
                        ]),
                    DeleteBulkAction::make()
                        ->visible(fn() => ! auth()->user()?->isMurid()),
                    \Filament\Actions\BulkAction::make('hitung_ulang')
                        ->label('Hitung Ulang Nilai')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            foreach ($records as $record) {
                                // Kosongkan Status_Nilai agar sistem mengkalkulasi ulang di model event
                                $record->Status_Nilai = null;
                                $record->save();
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Nilai berhasil dihitung ulang sesuai rumus terbaru')
                                ->success()
                                ->send();
                        })
                        ->visible(fn() => ! auth()->user()?->isMurid()),
                ]),
            ]);
    }
}
