<?php

namespace App\Filament\Resources\Nilais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class NilaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                \Filament\Tables\Columns\TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->mataPelajaranKurikulum->mataPelajaranMaster->kode_feeder ? "Kode MK: {$record->mataPelajaranKurikulum->mataPelajaranMaster->kode_feeder}" : 'Kode MK: Belum diset'),
                \Filament\Tables\Columns\TextColumn::make('dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('kelas.programKelas.nilai')
                    ->label('Program Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->description(fn($record) => $record->kelas && $record->kelas->kode_pddikti ? "Kode: {$record->kelas->kode_pddikti}" : 'Kode: Belum diset'),
                \Filament\Tables\Columns\TextColumn::make('kelas.semester')
                    ->label('Semester')
                    ->sortable()
                    ->formatStateUsing(fn($state) => "Semester {$state}"),
                \Filament\Tables\Columns\TextColumn::make('kelas.tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->sortable()
                    ->description(fn($record) => $record->kelas && $record->kelas->tahunAkademik && $record->kelas->tahunAkademik->kode_pddikti ? "Kode Smt: {$record->kelas->tahunAkademik->kode_pddikti}" : 'Kode Smt: Belum diset'),
                \Filament\Tables\Columns\TextColumn::make('jumlah_mahasiswa')
                    ->label('Jml Mahasiswa')
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(fn($record) => $record->siswaDataLjk()->count() . ' Siswa'),
                \Filament\Tables\Columns\TextColumn::make('nilai_akhir_student')
                    ->label('Nilai Akhir')
                    ->getStateUsing(function ($record) {
                        $user = auth()->user();
                        if ($user && $user->isMurid()) {
                            $ljk = $record->siswaDataLjk->first();
                            return $ljk ? ($ljk->Nilai_Akhir ?? '-') : '-';
                        }
                        return null;
                    })
                    ->visible(fn() => auth()->user() && auth()->user()->isMurid())
                    ->weight('bold'),
                \Filament\Tables\Columns\TextColumn::make('grade_student')
                    ->label('Grade')
                    ->getStateUsing(function ($record) {
                        $user = auth()->user();
                        if ($user && $user->isMurid()) {
                            $ljk = $record->siswaDataLjk->first();
                            return $ljk ? ($ljk->Nilai_Huruf ?? '-') : '-';
                        }
                        return null;
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'A', 'A-' => 'success',
                        'B+', 'B', 'B-' => 'info',
                        'C+', 'C', 'C-' => 'warning',
                        '-' => 'gray',
                        default => 'danger',
                    })
                    ->visible(fn() => auth()->user() && auth()->user()->isMurid()),
            ])
            ->filters([
                \App\Traits\HasGlobalTahunAkademikFilter::getGlobalTahunAkademikFilter('id_tahun_akademik', false, function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                    if (empty($data['value'])) return;
                    $query->whereHas('kelas', function ($q) use ($data) {
                        $q->where('id_tahun_akademik', $data['value']);
                    });
                }),
            ])
            ->recordActions([
                ViewAction::make()->label('Input Nilai')->icon('heroicon-o-pencil-square')->color('success'),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_all_pddikti')
                    ->label('Export Semua PDDIKTI')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        $export = new \App\Exports\NilaiPddiktiExport($query->pluck('mata_pelajaran_kelas.id')->toArray());
                        return \Maatwebsite\Excel\Facades\Excel::download($export, 'nilai_pddikti_semua_' . date('YmdHis') . '.xlsx');
                    }),
                \Filament\Actions\Action::make('import_pddikti')
                    ->label('Import PDDIKTI')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('File Excel Template PDDIKTI')
                            ->disk('local')
                            ->directory('imports')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required(),
                    ])
                    ->action(function (array $data, \Filament\Notifications\Notification $notification) {
                        try {
                            $filePath = storage_path('app/' . $data['file']);
                            $import = new \App\Imports\NilaiPddiktiImport();
                            \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                            $msg = "Berhasil: {$import->successCount} data. Gagal: {$import->failCount} data.";
                            if ($import->failCount > 0) {
                                $msg .= " Silakan cek log untuk detail kesalahan.";
                                foreach ($import->errors as $error) {
                                    \Illuminate\Support\Facades\Log::warning("PDDIKTI Import Error: " . $error);
                                }
                            }

                            $notification->title('Import Selesai')
                                ->body($msg)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            $notification->title('Import Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('export_pddikti')
                        ->label('Export PDDIKTI')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $export = new \App\Exports\NilaiPddiktiExport($records->pluck('id')->toArray());
                            return \Maatwebsite\Excel\Facades\Excel::download($export, 'nilai_pddikti_terpilih_' . date('YmdHis') . '.xlsx');
                        }),
                ]),
            ])
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query) {
                $user = auth()->user();
                
                $eagerLoads = [
                    'mataPelajaranKurikulum.mataPelajaranMaster',
                    'dosenData',
                    'kelas.programKelas',
                    'kelas.tahunAkademik'
                ];

                if ($user && $user->isMurid()) {
                    $eagerLoads['siswaDataLjk'] = function ($q) use ($user) {
                        $q->whereHas('akademikKrs.riwayatPendidikan.siswaData', fn($sq) => $sq->where('user_id', $user->id));
                    };
                }

                $query->with($eagerLoads);

                if ($user && $user->isPengajar()) {
                    $query->where('id_dosen_data', $user->getDosenId());
                }
            });
    }
}
