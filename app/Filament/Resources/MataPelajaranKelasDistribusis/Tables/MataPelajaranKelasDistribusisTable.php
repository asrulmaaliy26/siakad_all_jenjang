<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MataPelajaranKelasDistribusisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: 'mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                //  TextColumn::make('id_mata_pelajaran_kurikulum')
                //     ->label('ID Kurikulum')
                //     ->numeric()
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('kelas.programKelas.nilai')
                    ->label('Program Kelas')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.kode_feeder')
                    ->label('Kode MK')
                    ->toggleable(),

                TextColumn::make('dosen.nama')
                    ->label('Dosen')
                    ->sortable()
                    ->toggleable(),

                // TextColumn::make('pengawas.nama')
                //     ->label('Pengawas')
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('ruangKelas.nilai')
                    ->label('Ruang')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('pelaksanaanKelas.nilai')
                    ->label('Pelaksanaan')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('hari')
                    ->label('Hari')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jam')
                    ->label('Jam')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('uts')
                    ->label('Jadwal UTS')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('uas')
                    ->label('Jadwal UAS')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('status_uts')
                    ->label('Status UTS')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ])
                    ->toggleable(),

                BadgeColumn::make('status_uas')
                    ->label('Status UAS')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ])
                    ->toggleable(),

                TextColumn::make('ruang_uts')
                    ->label('Ruang UTS')
                    ->toggleable(),

                TextColumn::make('ruang_uas')
                    ->label('Ruang UAS')
                    ->toggleable(),

                TextColumn::make('link_kelas')
                    ->label('Link Kelas')
                    ->url(fn($record) => $record->link_kelas ?: null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('passcode')
                    ->label('Passcode')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            // ->toolbarActions([])
            ->headerActions([
                \Filament\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\MataPelajaranKelasDistribusiImporter::class)
                    ->label('Import / Perbarui')
                    ->chunkSize(100),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
