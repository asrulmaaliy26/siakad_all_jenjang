<?php

namespace App\Filament\Resources\SarprasBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class SarprasBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                \Filament\Tables\Columns\TextColumn::make('kode_barang')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('nama_barang')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('jurusan.nama')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('kondisi')
                    ->searchable()
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('status_penggunaan')
                    ->searchable()
                    ->badge(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('sarpras_kategori_id')
                    ->relationship('kategori', 'nama_kategori')
                    ->label('Kategori'),
                \Filament\Tables\Filters\SelectFilter::make('fakultas')
                    ->relationship('jurusan.fakultas', 'nama')
                    ->label('Fakultas'),
                \Filament\Tables\Filters\SelectFilter::make('id_jurusan')
                    ->relationship('jurusan', 'nama')
                    ->label('Jurusan'),
                \Filament\Tables\Filters\SelectFilter::make('tahun_pengadaan')
                    ->label('Tahun Pengadaan')
                    ->options(function () {
                        return \App\Models\SarprasBarang::selectRaw('YEAR(tanggal_pengadaan) as year')
                            ->whereNotNull('tanggal_pengadaan')
                            ->distinct()
                            ->pluck('year', 'year')
                            ->toArray();
                    })
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['value'],
                            fn (\Illuminate\Database\Eloquent\Builder $query, $year): \Illuminate\Database\Eloquent\Builder => $query->whereYear('tanggal_pengadaan', $year),
                        );
                    }),
                \Filament\Tables\Filters\SelectFilter::make('status_penggunaan')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Digunakan' => 'Digunakan',
                        'Dipinjam' => 'Dipinjam',
                        'Dihapus' => 'Dihapus',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
