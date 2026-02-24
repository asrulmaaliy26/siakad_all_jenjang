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
use Filament\Tables\Actions\ButtonAction;
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
                                ->fromTable()
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
                \Filament\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\MataPelajaranMasterImporter::class)
                    ->label('Import')
                    ->chunkSize(100),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
