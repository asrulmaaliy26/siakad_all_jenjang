<?php

namespace App\Filament\Resources\RiwayatPendidikans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\BulkAction;
use App\Models\DosenData;
use App\Models\RefOption\StatusSiswa;
use App\Models\RefOption\ProgramKelas;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class RiwayatPendidikansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                // TextColumn::make('id')
                //     ->label('ID Riwayat')
                //     ->searchable()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('angkatan'),
                TextColumn::make('semester')
                    ->label('Smt')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn($record) => $record->getSemester())
                    ->sortable(),
                TextColumn::make('siswa.nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jurusan.nama')
                    // ->numeric()
                    ->sortable(),
                TextColumn::make('waliDosen.nama')
                    ->label('Wali Dosen')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('statusSiswa.nilai')
                    ->label('Status Siswa')
                    ->toggleable(false)
                    ->sortable(),
                // TextColumn::make('tanggal_mulai')
                //     ->date()
                //     ->sortable(),
                ToggleColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn($record) => in_array($record->status, ['Y', 'Aktif']))
                    ->updateStateUsing(function ($state, $record) {
                        // dd($record, $state);
                        $record->update([
                            'status' => $state ? 'Y' : 'N',
                        ]);
                    })
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('programKelas.nilai')
                    ->label('Program Kelas')
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Status Riwayat')
                    ->placeholder('Semua Riwayat (Termasuk Histori Lama)')
                    ->trueLabel('Hanya Riwayat Aktif')
                    ->falseLabel('Hanya Riwayat Lama (Tidak Aktif)')
                    ->queries(
                        true: fn(Builder $query) => $query->whereIn('status', ['Y', 'Aktif']),
                        false: fn(Builder $query) => $query->whereNotIn('status', ['Y', 'Aktif']),
                        blank: fn(Builder $query) => $query,
                    )
                    ->default(true),

                SelectFilter::make('angkatan')
                    ->label('Angkatan')
                    ->options(fn() => \App\Models\TahunAkademik::query()
                        ->select('nama')
                        ->get()
                        ->map(fn($t) => explode('/', explode(' ', $t->nama)[0])[0])
                        ->unique()
                        ->sortDesc()
                        ->mapWithKeys(fn($y) => [$y => $y])
                        ->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('tahunAkademik', function ($q) use ($data) {
                            $q->where('nama', 'like', $data['value'] . '/%');
                        });
                    })
                    ->default(
                        fn() => \App\Models\TahunAkademik::query()
                            ->select('nama')
                            ->get()
                            ->map(fn($t) => explode('/', explode(' ', $t->nama)[0])[0])
                            ->unique()
                            ->max()
                    ),
                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama'),
            ])
            ->recordActions([
                EditAction::make(),
                VIewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('set_wali_dosen')
                        ->label('Set Wali Dosen')
                        ->icon('heroicon-o-user-group')
                        ->form([
                            Select::make('id_wali_dosen')
                                ->label('Pilih Wali Dosen')
                                ->options(DosenData::pluck('nama', 'id'))
                                ->placeholder('Pilih Dosen...')
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update([
                                'id_wali_dosen' => $data['id_wali_dosen'],
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('set_angkatan')
                        ->label('Set Angkatan (Tahun Akademik)')
                        ->icon('heroicon-o-calendar')
                        ->color('info')
                        ->form([
                            Select::make('id_tahun_akademik')
                                ->label('Tahun Akademik')
                                ->options(\App\Models\TahunAkademik::orderBy('nama', 'desc')->get()->mapWithKeys(fn($ta) => [$ta->id => "{$ta->nama} - {$ta->periode}"]))
                                ->placeholder('Pilih Tahun Akademik...')
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update([
                                'id_tahun_akademik' => $data['id_tahun_akademik'],
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('set_status_siswa')
                        ->label('Set Status Siswa')
                        ->icon('heroicon-o-identification')
                        ->color('warning')
                        ->form([
                            Select::make('ro_status_siswa')
                                ->label('Status Siswa')
                                ->options(
                                    StatusSiswa::aktif()
                                        ->pluck('nilai', 'id')
                                )
                                ->placeholder('Pilih Status Siswa...')
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update([
                                'ro_status_siswa' => $data['ro_status_siswa'],
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('set_program_kelas')
                        ->label('Set Program Kelas')
                        ->icon('heroicon-o-academic-cap')
                        ->color('success')
                        ->form([
                            Select::make('ro_program_kelas')
                                ->label('Program Kelas')
                                ->options(
                                    ProgramKelas::aktif()
                                        ->pluck('nilai', 'id')
                                )
                                ->placeholder('Pilih Program Kelas...')
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update([
                                'ro_program_kelas' => $data['ro_program_kelas'],
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->fromModel()
                                ->except([
                                    'id_siswa_data',
                                    'id_jurusan',
                                    'id_wali_dosen',
                                    'ro_program_sekolah',
                                    'ro_program_kelas',
                                    'ro_status_siswa',
                                    'ro_jns_daftar',
                                    'ro_jns_keluar',
                                    'id_tahun_akademik'
                                ])
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('siswa_nama')
                                        ->heading('Nama Siswa 🔗')
                                        ->getStateUsing(fn($record) => $record->siswa?->nama),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('jurusan_nama')
                                        ->heading('Jurusan 🔗')
                                        ->getStateUsing(fn($record) => $record->jurusan?->nama),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('wali_dosen_nama')
                                        ->heading('Wali Dosen 🔗')
                                        ->getStateUsing(fn($record) => $record->waliDosen?->nama),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('status_siswa')
                                        ->heading('Status Siswa 🔗')
                                        ->getStateUsing(fn($record) => $record->statusSiswa?->nilai),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('program_kelas')
                                        ->heading('Program Kelas 🔗')
                                        ->getStateUsing(fn($record) => $record->programKelas?->nilai),
                                ]),
                        ]),
                    DeleteBulkAction::make(),
                ]),
            ])
            // ->toolbarActions([])
            ->headerActions([
                \Filament\Actions\Action::make('import')
                    ->label('Import Riwayat Pendidikans')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->storeFiles(false)
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $file = is_array($data['file']) ? $data['file'][0] : $data['file'];
                        $filePath = $file->getRealPath();
                        $import = new \App\Imports\RiwayatPendidikanImport();
                        \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                        \Filament\Notifications\Notification::make()
                            ->title('Import Selesai')
                            ->body($import->successCount . ' baris berhasil diimpor.')
                            ->success()
                            ->send();
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromModel()
                            ->except([
                                'id_siswa_data',
                                'id_jurusan',
                                'id_wali_dosen',
                                'ro_program_sekolah',
                                'ro_program_kelas',
                                'ro_status_siswa',
                                'ro_jns_daftar',
                                'ro_jns_keluar',
                                'id_tahun_akademik'
                            ])
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('siswa_nama')
                                    ->heading('Nama Siswa 🔗')
                                    ->getStateUsing(fn($record) => $record->siswa?->nama),
                                \pxlrbt\FilamentExcel\Columns\Column::make('jurusan_nama')
                                    ->heading('Jurusan 🔗')
                                    ->getStateUsing(fn($record) => $record->jurusan?->nama),
                                \pxlrbt\FilamentExcel\Columns\Column::make('wali_dosen_nama')
                                    ->heading('Wali Dosen 🔗')
                                    ->getStateUsing(fn($record) => $record->waliDosen?->nama),
                                \pxlrbt\FilamentExcel\Columns\Column::make('status_siswa')
                                    ->heading('Status Siswa 🔗')
                                    ->getStateUsing(fn($record) => $record->statusSiswa?->nilai),
                                \pxlrbt\FilamentExcel\Columns\Column::make('program_kelas')
                                    ->heading('Program Kelas 🔗')
                                    ->getStateUsing(fn($record) => $record->programKelas?->nilai),
                            ]),
                    ])
            ]);
    }
}
