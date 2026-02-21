<?php

namespace App\Filament\Resources\TaPengajuanJuduls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaPengajuanJudulsTable
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
                    ->label('Judul')
                    ->limit(60)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->sortable(),

                TextColumn::make('dosenReview.nama')
                    ->label('Reviewer')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tgl Pengajuan')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tgl_ujian')
                    ->label('Tgl Ujian')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray'    => 'pending',
                        'success' => 'disetujui',
                        'danger'  => 'ditolak',
                        'warning' => 'revisi',
                        'info'    => 'selesai',
                    ]),

                TextColumn::make('nilai_rata_rata')
                    ->label('Nilai Rata-rata')
                    ->toggleable(isToggledHiddenByDefault: true),
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

                SelectFilter::make('id_dosen_review')
                    ->label('Reviewer')
                    ->relationship('dosenReview', 'nama')
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
