<?php

namespace App\Filament\Resources\TaSkripsis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaSkripsisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('riwayatPendidikan.siswa.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('riwayatPendidikan.nomor_induk')
                    ->label('NIM')
                    ->searchable(),

                TextColumn::make('judul')
                    ->label('Judul Skripsi')
                    ->limit(60)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->sortable(),

                TextColumn::make('tgl_ujian')
                    ->label('Tgl Sidang')
                    ->date('d/m/Y')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray'    => 'pending',
                        'success' => 'disetujui',
                        'danger'  => 'ditolak',
                        'warning' => 'revisi',
                        'info'    => 'selesai',
                    ]),

                TextColumn::make('nilai_akhir')
                    ->label('Nilai Akhir')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pending',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                        'revisi'    => 'Perlu Revisi',
                        'selesai'   => 'Selesai',
                    ]),

                SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->relationship('tahunAkademik', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
