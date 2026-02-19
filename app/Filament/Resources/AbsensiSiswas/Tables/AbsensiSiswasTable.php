<?php

namespace App\Filament\Resources\AbsensiSiswas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;

class AbsensiSiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //indexing table
                TextColumn::make('No')
                    ->label('No')
                    ->getStateUsing(
                        fn($rowLoop) => $rowLoop->iteration
                    ),
                TextColumn::make('krs.riwayatPendidikan.siswa.nomor_induk')
                    ->label('NIS / NIM')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('krs.riwayatPendidikan.siswa.nama')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable(),

                TextColumn::make(
                    'pertemuan.mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama'
                )
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('krs.kelas.programKelas.nama')
                    ->label('Program Kelas')
                    ->sortable(),

                TextColumn::make('krs.kelas.semester')
                    ->label('Semester')
                    ->sortable(),

                TextColumn::make('pertemuan.pertemuan_ke')
                    ->label('Pertemuan')
                    ->sortable(),

                TextColumn::make('pertemuan.tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->sortable(),

            ])
            // ->columns([
            //     TextColumn::make('id_pertemuan')
            //         ->numeric()
            //         ->sortable(),
            //     TextColumn::make('id_krs')
            //         ->numeric()
            //         ->sortable(),
            //     TextColumn::make('status'),
            //     TextColumn::make('waktu_absen')
            //         ->dateTime()
            //         ->sortable(),
            //     TextColumn::make('created_at')
            //         ->dateTime()
            //         ->sortable()
            //         ->toggleable(isToggledHiddenByDefault: true),
            //     TextColumn::make('updated_at')
            //         ->dateTime()
            //         ->sortable()
            //         ->toggleable(isToggledHiddenByDefault: true),
            // ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
