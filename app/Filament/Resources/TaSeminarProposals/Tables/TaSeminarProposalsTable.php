<?php

namespace App\Filament\Resources\TaSeminarProposals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\DosenData;
use Filament\Facades\Filament;

class TaSeminarProposalsTable
{
    public static function configure(Table $table): Table
    {
        $user    = auth()->user();
        if (!$user instanceof \App\Models\User) return $table;
        $isPengajar = $user->isPengajar();
        $dosenId = $isPengajar ? $user->getDosenId() : null;

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

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'revisi' => 'warning',
                        'selesai' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-m-clock',
                        'disetujui' => 'heroicon-m-check-circle',
                        'ditolak' => 'heroicon-m-x-circle',
                        'revisi' => 'heroicon-m-arrow-path',
                        'selesai' => 'heroicon-m-flag',
                        default => 'heroicon-m-question-mark-circle',
                    }),

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
                    ->default(\App\Models\TahunAkademik::where('status', 'Aktif')->first()?->id)
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('cetak_kartu_bimbingan')
                    ->label('Cetak Kartu Bimbingan Skripsi')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('cetak.kartu-bimbingan.sempro', $record->id))
                    ->openUrlInNewTab(),
                EditAction::make()
                    // Dosen pengajar tidak bisa edit — hanya admin
                    ->visible(!$isPengajar),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make()
                        ->visible(!$isPengajar)
                        ->disabled(fn() => auth()->user() && auth()->user()->isMurid()),
                ]),
            ]);
    }
}
