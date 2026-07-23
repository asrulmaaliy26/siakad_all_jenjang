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
            ->recordClasses(fn($record, $livewire) => match($livewire->activeTab ?? null) {
                'today' => $record->hasAbsensiToday() ? null : 'bg-red-50/50 dark:bg-red-900/20',
                'this_week' => $record->hasAbsensiThisWeek() ? null : 'bg-red-50/50 dark:bg-red-900/20',
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
                        'today' => $record->hasAbsensiToday() ? 'success' : 'danger',
                        'this_week' => $record->hasAbsensiThisWeek() ? 'success' : 'danger',
                        default => null,
                    })
                    ->description(fn($record, $livewire) => match($livewire->activeTab ?? null) {
                        'today' => $record->hasAbsensiToday() ? 'Sudah mengisi hari ini' : 'BELUM MENGISI HARI INI',
                        'this_week' => $record->hasAbsensiThisWeek() ? 'Sudah mengisi minggu ini' : 'BELUM MENGISI MINGGU INI',
                        default => null,
                    }),
                TextColumn::make('jumlah_mahasiswa')
                    ->label('Mhs')
                    ->sortable(),
                TextColumn::make('jumlah_sesi_absensi')
                    ->label('Sesi')
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->numeric(1)
                    ->suffix('%')
                    ->sortable()
                    ->description(fn($record) => number_format($record->progress, 1) . '% dari 16'),
            ])
            ->filters([
                SelectFilter::make('id_tahun_akademik')->default(fn () => session('global_tahun_akademik_id') ?? \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->label('Tahun Akademik')
                    ->options(fn() => \App\Models\TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $query->whereHas('kelas', function ($q) use ($data) {
                            $q->where('id_tahun_akademik', $data['value']);
                        });
                    })
                    
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
