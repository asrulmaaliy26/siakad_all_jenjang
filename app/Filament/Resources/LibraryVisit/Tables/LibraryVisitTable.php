<?php

namespace App\Filament\Resources\LibraryVisit\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LibraryVisitTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('riwayatPendidikan.siswaData.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('riwayatPendidikan.nomor_induk')
                    ->label('NIM/NPM')
                    ->searchable(),
                TextColumn::make('visited_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->label('Keperluan')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
