<?php

namespace App\Filament\Resources\RiwayatPendidikans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;

class RiwayatPendidikansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('angkatan'),
                TextColumn::make('siswa.nama')
                    ->label('Nama')
                    // ->numeric()
                    ->sortable(),
                TextColumn::make('nomor_induk')
                    ->sortable(),
                TextColumn::make('jenjangPendidikan.nama')
                    // ->numeric()
                    ->sortable(),
                TextColumn::make('jurusan.nama')
                    // ->numeric()
                    ->sortable(),
                TextColumn::make('statusSiswa.nilai')
                    ->label('Status Siswa')
                    ->toggleable(false)
                    ->sortable(),
                // TextColumn::make('tanggal_mulai')
                //     ->date()
                //     ->sortable(),
                ToggleColumn::make('status')
                    ->label('Status')
                    // ->getStateUsing(fn($record) => $record->status === 'Y')
                    ->updateStateUsing(function ($state, $record) {
                        // dd($record, $state);
                        $record->update([
                            'status' => $state ? 'Y' : 'N',
                        ]);
                    })
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('tanggal_selesai')
                    ->date()
                    ->sortable(),
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
            ])
            ->recordActions([
                EditAction::make(),
                VIewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            // ->toolbarActions([])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
