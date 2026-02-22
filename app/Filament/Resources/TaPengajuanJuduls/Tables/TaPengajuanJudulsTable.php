<?php

namespace App\Filament\Resources\TaPengajuanJuduls\Tables;

use App\Models\DosenData;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaPengajuanJudulsTable
{
    public static function configure(Table $table): Table
    {
        $user    = Filament::auth()->user();
        $isPengajar = $user && $user->isPengajar();
        $dosenId = $isPengajar
            ? DosenData::where('user_id', $user->id)->value('id')
            : null;

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
                    ->formatStateUsing(fn($record) => $record->tahunAkademik ? $record->tahunAkademik->nama . ' - ' . $record->tahunAkademik->periode : '-')
                    ->sortable(),

                // Kolom posisi pembimbing — hanya tampil untuk dosen pengajar
                TextColumn::make('posisi_pembimbing')
                    ->label('Posisi Anda')
                    ->state(function ($record) use ($dosenId) {
                        if (!$dosenId) return null;
                        if ($record->id_dosen_pembimbing_1 == $dosenId) return 'Pembimbing 1';
                        if ($record->id_dosen_pembimbing_2 == $dosenId) return 'Pembimbing 2';
                        if ($record->id_dosen_pembimbing_3 == $dosenId) return 'Pembimbing 3';
                        return null;
                    })
                    ->badge()
                    ->color('info')
                    ->visible($isPengajar),

                // Pembimbing 1/2/3 — hanya tampil untuk admin
                TextColumn::make('dosenPembimbing1.nama')
                    ->label('Pembimbing 1')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(!$isPengajar),

                TextColumn::make('dosenPembimbing2.nama')
                    ->label('Pembimbing 2')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(!$isPengajar),

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
                    ->options(\App\Models\TahunAkademik::all()->mapWithKeys(fn($t) => [$t->id => $t->nama . ' - ' . $t->periode]))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    // Dosen pengajar tidak bisa edit — hanya admin
                    ->visible(!$isPengajar),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(!$isPengajar),
                ]),
            ]);
    }
}
