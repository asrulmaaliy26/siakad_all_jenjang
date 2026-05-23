<?php

namespace App\Filament\Resources\MonitoringJurnalResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class MonitoringJurnalTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordClasses(fn($record, $livewire) => match($livewire->activeTab ?? null) {
                'today' => $record->hasJurnalToday() ? null : 'bg-red-50/50 dark:bg-red-900/20',
                'this_week' => $record->hasJurnalThisWeek() ? null : 'bg-red-50/50 dark:bg-red-900/20',
                default => null,
            })
            ->columns([
                TextColumn::make('No')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hari_jam')
                    ->label('Jadwal')
                    ->getStateUsing(fn($record) => ($record->hari ?? '-') . ' / ' . ($record->jam ?? '-'))
                    ->color('info'),
                TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn($record, $livewire) => match($livewire->activeTab ?? null) {
                        'today' => $record->hasJurnalToday() ? 'success' : 'danger',
                        'this_week' => $record->hasJurnalThisWeek() ? 'success' : 'danger',
                        default => null,
                    })
                    ->description(fn($record, $livewire) => match($livewire->activeTab ?? null) {
                        'today' => $record->hasJurnalToday() ? 'Sudah mengisi jurnal hari ini' : 'BELUM MENGISI JURNAL HARI INI',
                        'this_week' => $record->hasJurnalThisWeek() ? 'Sudah mengisi jurnal minggu ini' : 'BELUM MENGISI JURNAL MINGGU INI',
                        default => null,
                    }),
                TextColumn::make('jumlah_materi')
                    ->label('Materi')
                    ->sortable(),
                TextColumn::make('jumlah_tugas')
                    ->label('Tugas')
                    ->sortable(),
                IconColumn::make('has_rps')
                    ->label('RPS')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
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
                SelectFilter::make('id_kelas')
                    ->label('Kelas')
                    ->options(function () {
                        return \App\Models\Kelas::with(['jurusan', 'programKelas'])
                            ->get()
                            ->mapWithKeys(function ($kelas) {
                                $nama = ($kelas->jurusan?->nama ?? '-') . ' - ' . ($kelas->programKelas?->nilai ?? '-') . ' (Smt ' . $kelas->semester . ')';
                                return [$kelas->id => $nama];
                            });
                    })
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
