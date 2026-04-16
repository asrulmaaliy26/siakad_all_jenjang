<?php

namespace App\Filament\Resources\MonitoringAbsensi\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class MonitoringAbsensiTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_mahasiswa')
                    ->label('Jumlah Mahasiswa')
                    ->sortable(),
                TextColumn::make('jumlah_sesi_absensi')
                    ->label('Sesi Absensi')
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progress (%)')
                    ->numeric(1)
                    ->suffix('%')
                    ->sortable()
                    ->description(fn($record) => number_format($record->progress, 1) . '% dari 16 pertemuan'),
            ])
            ->filters([
                SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(fn() => \App\Models\TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $query->whereHas('kelas', function ($q) use ($data) {
                            $q->where('id_tahun_akademik', $data['value']);
                        });
                    })
                    ->default(fn() => \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->searchable(),
                SelectFilter::make('id_dosen_data')
                    ->label('Dosen')
                    ->relationship('dosenData', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
