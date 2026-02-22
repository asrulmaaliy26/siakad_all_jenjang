<?php

namespace App\Filament\Resources\MataPelajaranKelas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MataPelajaranKelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kelas.semester')
                    ->label('Semester')
                    ->sortable(),
                TextColumn::make('dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hari')
                    ->searchable(),
                TextColumn::make('jam'),
                TextColumn::make('ruangKelas.nilai')
                    ->label('Ruang'),
                TextColumn::make('jumlah')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status_uts')
                    ->label('UTS')
                    ->boolean(),
                IconColumn::make('status_uas')
                    ->label('UAS')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->disabled(fn() => auth()->user()?->isMurid()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make()
                        ->disabled(fn() => auth()->user()?->isMurid()),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
