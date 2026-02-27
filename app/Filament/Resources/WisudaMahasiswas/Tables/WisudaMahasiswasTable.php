<?php

namespace App\Filament\Resources\WisudaMahasiswas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WisudaMahasiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('riwayatPendidikan.nomor_induk')
                    ->label('NIM')
                    ->searchable(),
                TextColumn::make('periodeWisuda.periode_ke')
                    ->label('Periode Wisuda')
                    ->badge()
                    ->sortable(),
                IconColumn::make('bebas_prodi')
                    ->label('Prodi')
                    ->boolean(),
                IconColumn::make('bebas_fakultas')
                    ->label('Fakultas')
                    ->boolean(),
                IconColumn::make('bebas_perpustakaan')
                    ->label('Perpus')
                    ->boolean(),
                IconColumn::make('bebas_keuangan')
                    ->label('Keuangan')
                    ->boolean(),
                TextColumn::make('status_pendaftaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->searchable(),
                TextColumn::make('tanggal_daftar')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
