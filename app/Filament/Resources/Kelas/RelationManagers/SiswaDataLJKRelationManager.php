<?php

namespace App\Filament\Resources\Kelas\RelationManagers;

use App\Models\MataPelajaranKelas;
use App\Models\SiswaDataLJK;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use App\Models\AkademikKrs;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ImportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MultiSelect;
use Filament\Actions\ActionGroup;

class SiswaDataLJKRelationManager extends RelationManager
{
    protected static string $relationship = 'siswaDataLjk';
    protected static ?string $title = 'Data LJK Siswa';

    public function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('akademikKrs.riwayatPendidikan.siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('id')
                    ->label('ID LJK')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('id_akademik_krs')
                    ->label('ID KRS')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('id_mata_pelajaran_kelas')
                    ->label('ID Mapel Kelas')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('akademikKrs.riwayatPendidikan.nomor_induk')
                    ->label('NIM')
                    ->searchable(),

                TextColumn::make('akademikKrs.riwayatPendidikan.programKelas.nilai')
                    ->label('Program Kelas')
                    ->badge()
                    ->color('info'),

                TextColumn::make('semester_now')
                    ->label('Semester')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(fn($record) => 'Sem ' . ($record->akademikKrs?->riwayatPendidikan?->getSemester() ?? '-')),

                TextColumn::make('mapel_progress')
                    ->label('Mapel Diikuti')
                    ->badge()
                    ->getStateUsing(function ($record, RelationManager $livewire) {
                        $kelas      = $livewire->getOwnerRecord();
                        $totalMapel = $kelas->mataPelajaranKelas()->count();
                        $krsId      = $record->id_akademik_krs;

                        $jumlahLjk  = SiswaDataLJK::where('id_akademik_krs', $krsId)
                            ->whereHas('mataPelajaranKelas', fn($q) => $q->where('id_kelas', $kelas->id))
                            ->count();

                        return "{$jumlahLjk} / {$totalMapel} Mapel";
                    })
                    ->color(function ($record, RelationManager $livewire) {
                        $kelas      = $livewire->getOwnerRecord();
                        $totalMapel = $kelas->mataPelajaranKelas()->count();
                        $krsId      = $record->id_akademik_krs;

                        $jumlahLjk  = SiswaDataLJK::where('id_akademik_krs', $krsId)
                            ->whereHas('mataPelajaranKelas', fn($q) => $q->where('id_kelas', $kelas->id))
                            ->count();

                        return $jumlahLjk >= $totalMapel ? 'success' : 'warning';
                    }),

                TextColumn::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Nilai_Akhir')
                    ->label('Nilai Akhir')
                    ->sortable(),
                TextColumn::make('Nilai_Huruf')
                    ->label('Grade'),
                TextColumn::make('Status_Nilai')
                    ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('id_mata_pelajaran_kelas')
                    ->label('Mata Pelajaran Kelas')
                    ->options(function (RelationManager $livewire) {
                        return MataPelajaranKelas::where('id_kelas', $livewire->getOwnerRecord()->id)
                            ->with('mataPelajaranKurikulum.mataPelajaranMaster')
                            ->get()
                            ->mapWithKeys(fn($item) => [
                                $item->id => ($item->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? 'N/A') . ' (' . $item->id . ')'
                            ]);
                    })
                    ->default(function (RelationManager $livewire) {
                        return MataPelajaranKelas::where('id_kelas', $livewire->getOwnerRecord()->id)
                            ->first()?->id;
                    })
                    ->searchable()
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Mahasiswa ke LJK')
                    ->modalHeading('Tambah Mahasiswa ke Daftar Nilai (LJK)')
                    ->form([
                        Select::make('id_mata_pelajaran_kelas')
                            ->label('Mata Pelajaran Kelas')
                            ->options(function (RelationManager $livewire) {
                                return MataPelajaranKelas::where('id_kelas', $livewire->getOwnerRecord()->id)
                                    ->with('mataPelajaranKurikulum.mataPelajaranMaster')
                                    ->get()
                                    ->mapWithKeys(fn($item) => [
                                        $item->id => ($item->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? 'N/A') . ' (' . $item->id . ')'
                                    ]);
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('id_akademik_krs_ids', []))
                            ->default(function (RelationManager $livewire) {
                                // Default ke filter yang sedang aktif jika ada
                                return MataPelajaranKelas::where('id_kelas', $livewire->getOwnerRecord()->id)->first()?->id;
                            }),
                        Select::make('ro_program_kelas')
                            ->label('Filter Program Kelas')
                            ->options(\App\Models\RefOption\ProgramKelas::aktif()->pluck('nilai', 'id'))
                            ->default(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->ro_program_kelas)
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('id_akademik_krs_ids', [])),

                        MultiSelect::make('id_akademik_krs_ids')
                            ->label('Mahasiswa')
                            ->options(function (callable $get, RelationManager $livewire) {
                                $kelas = $livewire->getOwnerRecord();
                                return AkademikKrs::query()
                                    ->whereHas('riwayatPendidikan', function ($q) use ($kelas, $get) {
                                        $q->where('id_jurusan', $kelas->id_jurusan);
                                        if ($programKelasId = $get('ro_program_kelas')) {
                                            $q->where('ro_program_kelas', $programKelasId);
                                        }
                                    })
                                    ->where('status_aktif', 'Y')
                                    ->when($get('id_mata_pelajaran_kelas'), function ($query, $mapelId) {
                                        $query->whereDoesntHave('siswaDataLjk', function ($q) use ($mapelId) {
                                            $q->where('id_mata_pelajaran_kelas', $mapelId)
                                                ->where(function ($sub) {
                                                    $sub->where('Status_Nilai', '!=', 'TL')
                                                        ->orWhereNull('Status_Nilai');
                                                });
                                        });
                                    })
                                    ->with('riwayatPendidikan.siswa')
                                    ->get()
                                    ->mapWithKeys(fn($item) => [
                                        $item->id => ($item->riwayatPendidikan->siswa->nama ?? 'N/A') . ' (' . ($item->riwayatPendidikan->nomor_induk ?? '-') . ')'
                                    ]);
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->optionsLimit(500)
                            ->placeholder('Pilih mahasiswa dari jurusan yang sama'),
                    ])
                    ->using(function (array $data) {
                        $mapelId = $data['id_mata_pelajaran_kelas'];
                        $krsIds = $data['id_akademik_krs_ids'];

                        foreach ($krsIds as $krsId) {
                            $existing = SiswaDataLJK::where('id_akademik_krs', $krsId)
                                ->where('id_mata_pelajaran_kelas', $mapelId)
                                ->first();

                            // Jika sudah ada dan statusnya TL, hapus dulu agar bisa buat baru (reset)
                            if ($existing && $existing->Status_Nilai === 'TL') {
                                $existing->delete();
                            }

                            SiswaDataLJK::firstOrCreate([
                                'id_akademik_krs' => $krsId,
                                'id_mata_pelajaran_kelas' => $mapelId,
                            ]);
                        }
                        return null; // Menghentikan proses pembuatan record tunggal default
                    }),
                ActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                        ->label('Export Excel')
                        ->color('success')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswa.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')->heading('Mata Kuliah'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.dosenData.nama')->heading('Dosen Pengajar'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UTS')->heading('UTS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UAS')->heading('UAS'),
                                    ...\array_map(fn($i) => \pxlrbt\FilamentExcel\Columns\Column::make("Nilai_TGS_{$i}")->heading("TGS $i"), \range(1, 12)),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_Performance')->heading('Perf'),
                                ])
                        ]),
                    \Filament\Actions\Action::make('importExcelCustom')
                        ->label('Import Excel')
                        ->color('warning')
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
                            $import = new \App\Imports\SiswaDataLJKImport();
                            \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                            \Filament\Notifications\Notification::make()
                                ->title('Import Selesai')
                                ->body($import->successCount . ' baris berhasil diimpor.')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Opsi Excel')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button(),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id')->heading('ID LJK'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.siswa.nama')->heading('Nama Mahasiswa'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('akademikKrs.riwayatPendidikan.nomor_induk')->heading('NIM'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_akademik_krs')->heading('ID KRS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('id_mata_pelajaran_kelas')->heading('ID Mapel Kelas'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')->heading('Mata Kuliah'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('mataPelajaranKelas.dosenData.nama')->heading('Dosen Pengajar'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UTS')->heading('UTS'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_UAS')->heading('UAS'),
                                    ...\array_map(fn($i) => \pxlrbt\FilamentExcel\Columns\Column::make("Nilai_TGS_{$i}")->heading("TGS $i"), \range(1, 12)),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('Nilai_Performance')->heading('Perf'),
                                ])
                        ]),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}



