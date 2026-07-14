<?php

namespace App\Filament\Resources\MataPelajaranMasters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Maatwebsite\Excel\Excel;
use Filament\Actions\ButtonAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class MataPelajaranMastersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('kode_feeder')
                    ->searchable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('jurusan.nama')
                    // ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bobot')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                SelectFilter::make('jurusan_id')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('jenis')
                    ->label('Jenis Mapel')
                    ->options([
                        'wajib' => 'Wajib',
                        'peminatan' => 'Peminatan',
                    ]),

                // SelectFilter::make('nama')
                //     ->label('Nama Mapel')
                //     ->options(
                //         MataPelajaranMaster::query()
                //             ->pluck('nama', 'nama')
                //             ->toArray()
                //     )
                //     ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([

                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),

                    // ======================
                    // EXPORT PDF (BULK)
                    // ======================
                    ExportBulkAction::make('cetak_pdf')
                        ->label('Cetak PDF')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {

                            $pdf = Pdf::loadView(
                                'exports.mata_pelajaran_pdf',
                                [
                                    'records' => $records,
                                ]
                            )->setPaper('A4', 'landscape');

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'mata-pelajaran-' . now()->format('Y-m-d') . '.pdf'
                            );
                        }),

                    // ======================
                    // EXPORT EXCEL (BULK)
                    // ======================
                    ExportBulkAction::make('export_excel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->exports([
                            ExcelExport::make()
                                ->withColumns([
                                    Column::make('kode_feeder')->heading('Kode'),
                                    Column::make('nama')->heading('Nama Mata Pelajaran'),
                                    Column::make('jurusan.nama')->heading('Jurusan'),
                                    Column::make('bobot')->heading('Bobot'),
                                    Column::make('jenis')->heading('Jenis'),
                                ])
                                ->withFilename(
                                    fn() =>
                                    'mata-pelajaran-excel-' . now()->format('Y-m-d')
                                ),
                        ]),
                    DeleteBulkAction::make(),

                ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('import')
                    ->label('Import MP Master')
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
                        $import = new \App\Imports\MataPelajaranMasterImport();
                        \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                        \Filament\Notifications\Notification::make()
                            ->title('Import Selesai')
                            ->body($import->successCount . ' baris berhasil diimpor.')
                            ->success()
                            ->send();
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}

