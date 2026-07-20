<?php

namespace App\Filament\Resources\Akms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class AkmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.jurusan.nama')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.tahunAkademik.nama')
                    ->label('Angkatan')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->getStateUsing(fn($record) => $record->riwayatPendidikan?->getSemester($record->tgl_krs ?? $record->created_at, $record->id_tahun_akademik))
                    ->badge()
                    ->color('info'),
                \Filament\Tables\Columns\TextColumn::make('status_aktif')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->status_aktif == 'Y' ? 'Aktif' : 'Tidak Aktif')
                    ->color(fn($state) => $state === 'Aktif' ? 'success' : 'danger'),
                \Filament\Tables\Columns\TextColumn::make('sks_diambil')
                    ->label('SKS SMT')
                    ->badge()
                    ->color('warning'),
                \Filament\Tables\Columns\TextColumn::make('sks_total')
                    ->label('SKS Total')
                    ->badge()
                    ->color('primary'),
                \Filament\Tables\Columns\TextColumn::make('ips')
                    ->label('IPS')
                    ->weight('bold'),
                \Filament\Tables\Columns\TextColumn::make('ipk')
                    ->label('IPK')
                    ->weight('bold'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(fn() => \App\Models\TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
                    ->default(fn() => \App\Models\TahunAkademik::orderByDesc('id')->first()?->id)
                    ->searchable()
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()->label('Detail AKM'),
            ])
            ->toolbarActions([
                //
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->label('Export Excel')
                        ->color('success')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('riwayatPendidikan.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('riwayatPendidikan.siswaData.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('riwayatPendidikan.jurusan.nama')->heading('Program Studi'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('tahunAkademik.nama')->heading('Tahun Akademik (KRS)'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('semester')->heading('Semester')
                                        ->getStateUsing(fn($record) => $record->riwayatPendidikan?->getSemester($record->tgl_krs ?? $record->created_at, $record->id_tahun_akademik)),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('sks_diambil')->heading('SKS SMT'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('sks_total')->heading('SKS Total'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('ips')->heading('IPS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('ipk')->heading('IPK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('status_aktif')->heading('Status')
                                        ->getStateUsing(fn($record) => $record->status_aktif == 'Y' ? 'Aktif' : 'Tidak Aktif'),
                                ])
                                ->withFilename(fn() => 'Export_AKM_' . now()->format('YmdHis')),
                        ]),
                ]),
            ])
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query) {
                $query->with([
                    'riwayatPendidikan.siswaData',
                    'riwayatPendidikan.jurusan',
                    'tahunAkademik',
                    'siswaDataLjk.mataPelajaranKelas.kelas',
                    'siswaDataLjk.mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster'
                ]);
                
                // If the user is a student, only show their own AKM
                $user = auth()->user();
                if ($user && $user->isMurid()) {
                    $query->whereHas('riwayatPendidikan.siswaData', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            });
    }
}
