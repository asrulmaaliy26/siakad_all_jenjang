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
                TextColumn::make('id')
                    ->label('ID LJK')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('id_akademik_krs')
                    ->label('ID KRS')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('id_mata_pelajaran_kelas')
                    ->label('ID Mapel Kelas')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn() => auth()->user()?->isMurid()),
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('nilai')
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_UTS')
                    ->label('UTS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                ...array_map(fn($i) => TextInputColumn::make("Nilai_TGS_{$i}")
                    ->label("TGS $i")
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: $i > 1)
                    ->disabled(fn() => auth()->user()?->isMurid()), range(1, 12)),
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
                    ->visible(fn() => ! auth()->user()?->isMurid())
                    ->action(function () {
                        $record = $this->getOwnerRecord();
                        $krsList = AkademikKRS::where('id_kelas', $record->id_kelas)->get();

                        foreach ($krsList as $krs) {
                            SiswaDataLJK::firstOrCreate([
                                'id_mata_pelajaran_kelas' => $record->id,
                                'id_akademik_krs'         => $krs->id,
                            ], [
                                'nilai' => 0,
                            ]);
                        }

                        Notification::make()
                            ->title('Data berhasil disinkronisasi')
                            ->success()
                            ->send();
                    }),
                ActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                        ->label('Export Excel')
                        ->color('success')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->fromTable()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UTS')->heading('UTS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UAS')->heading('UAS'),
                                    ...\array_map(fn($i) => \pxlrbt\FilamentExcel\Columns\Column::make("Nilai_TGS_{$i}")->heading("TGS $i"), \range(1, 12)),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_Performance')->heading('Perf'),
                                ])
                                ->withFilename(function () {
                                    $record = $this->getOwnerRecord();
                                    return 'Export_Nilai_' . str($record->mataPelajaranKurikulum->mataPelajaranMaster->nama)->slug('_') . '_' . now()->format('YmdHis');
                                }),
                        ]),
                    ImportSiswaDataLjkAction::make()
                        ->visible(fn() => ! auth()->user()?->isMurid()),
                    Action::make('cetak_pdf')
                        ->label('Cetak PDF Resmi')
                        ->icon('heroicon-o-printer')
                        ->color('danger')
                        ->visible(fn() => ! auth()->user()?->isMurid())
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
                                ->fromTable()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
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
                ]),
            ]);
    }
}
