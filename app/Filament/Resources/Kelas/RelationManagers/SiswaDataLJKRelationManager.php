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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MultiSelect;

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
                TextColumn::make('akademikKrs.riwayatPendidikan.siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('akademikKrs.riwayatPendidikan.tanggal_mulai')
                    ->label('Semester')
                    ->formatStateUsing(fn($record) => $record->akademikKrs?->riwayatPendidikan?->getSemester()),
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
                        Select::make('ro_program_sekolah')
                            ->label('Filter Program Kelas')
                            ->options(\App\Models\RefOption\ProgramKelas::pluck('nilai', 'id'))
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('id_akademik_krs_ids', [])),

                        MultiSelect::make('id_akademik_krs_ids')
                            ->label('Mahasiswa')
                            ->options(function (callable $get, RelationManager $livewire) {
                                $kelas = $livewire->getOwnerRecord();
                                return AkademikKrs::query()
                                    ->whereHas('riwayatPendidikan', function ($q) use ($kelas, $get) {
                                        $q->where('id_jurusan', $kelas->id_jurusan);
                                        if ($programId = $get('ro_program_sekolah')) {
                                            $q->where('ro_program_sekolah', $programId);
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
                    })
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
