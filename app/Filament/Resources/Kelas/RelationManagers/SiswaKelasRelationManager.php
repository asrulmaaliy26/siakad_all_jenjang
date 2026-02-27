<?php

namespace App\Filament\Resources\Kelas\RelationManagers;

use App\Models\AkademikKrs;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\Jurusan;
use App\Models\Kurikulum;
use App\Models\MataPelajaranKurikulum;
use App\Models\RiwayatPendidikan;
use App\Models\SiswaDataLJK;

class SiswaKelasRelationManager extends RelationManager
{
    protected static string $relationship = 'siswaDataLjk';
    protected static ?string $title = 'Siswa/Mahasiswa';

    protected function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select('siswa_data_ljk.*')
            ->join('akademik_krs', 'siswa_data_ljk.id_akademik_krs', '=', 'akademik_krs.id')
            ->groupBy('siswa_data_ljk.id_akademik_krs');
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('filter_semester')
                ->label('Semester (Filter)')
                ->options(array_combine(range(1, 8), range(1, 8)))
                ->reactive()
                ->afterStateUpdated(fn($set) => $set('riwayat_pendidikan_ids', [])),

            Select::make('ro_program_sekolah')
                ->label('Program Kelas (Filter)')
                ->options(\App\Models\RefOption\ProgramKelas::pluck('nilai', 'id'))
                ->reactive()
                ->afterStateUpdated(fn($set) => $set('riwayat_pendidikan_ids', [])),

            MultiSelect::make('riwayat_pendidikan_ids')
                ->label('Pilih Siswa/Mahasiswa')
                ->required()
                ->searchable()
                ->preload(false)
                ->optionsLimit(20)
                ->reactive()

                // Saat dropdown dibuka (tanpa search)
                ->options(function (callable $get, RelationManager $livewire) {
                    $kelas = $livewire->getOwnerRecord();
                    $jurusanId = $get('id_jurusan') ?? $kelas->id_jurusan;
                    $programId = $get('ro_program_sekolah');

                    $query = RiwayatPendidikan::query()
                        ->where('id_jurusan', $jurusanId)
                        ->with('siswa');

                    if ($programId) {
                        $query->where('ro_program_sekolah', $programId);
                    }

                    // Hanya yang punya KRS aktif
                    $query->whereHas('akademikKrs', function ($q) {
                        $q->where('status_aktif', 'Y');
                    });

                    // Hanya yang belum punya LJK di kelas ini sama sekali (kecuali statusnya 'TL')
                    $query->whereDoesntHave('akademikKrs.siswaDataLjk', function ($q) use ($kelas) {
                        $q->whereHas('mataPelajaranKelas', function ($sub) use ($kelas) {
                            $sub->where('id_kelas', $kelas->id);
                        })
                            ->where(function ($sub) {
                                $sub->where('Status_Nilai', '!=', 'TL')
                                    ->orWhereNull('Status_Nilai');
                            });
                    });

                    $results = $query->limit(100)->get();

                    if ($semester = $get('filter_semester')) {
                        $results = $results->filter(fn(\App\Models\RiwayatPendidikan $item) => $item->getSemester() == $semester);
                    }

                    return $results
                        ->take(20)
                        ->mapWithKeys(fn(\App\Models\RiwayatPendidikan $item) => [
                            $item->id => $item->siswa?->nama . ' (Sem ' . $item->getSemester() . ')' ?? '-'
                        ])
                        ->toArray();
                })
                ->getSearchResultsUsing(function (string $search, callable $get, RelationManager $livewire) {
                    $kelas = $livewire->getOwnerRecord();
                    $jurusanId = $get('id_jurusan') ?? $kelas->id_jurusan;
                    $programId = $get('ro_program_sekolah');

                    $query = RiwayatPendidikan::query()
                        ->where('id_jurusan', $jurusanId)
                        ->with('siswa')
                        ->whereHas('siswa', function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%");
                        });

                    if ($programId) {
                        $query->where('ro_program_sekolah', $programId);
                    }

                    $query->whereHas('akademikKrs', function ($q) {
                        $q->where('status_aktif', 'Y');
                    });

                    $query->whereDoesntHave('akademikKrs.siswaDataLjk', function ($q) use ($kelas) {
                        $q->whereHas('mataPelajaranKelas', function ($sub) use ($kelas) {
                            $sub->where('id_kelas', $kelas->id);
                        })
                            ->where(function ($sub) {
                                $sub->where('Status_Nilai', '!=', 'TL')
                                    ->orWhereNull('Status_Nilai');
                            });
                    });

                    $results = $query->limit(100)->get();

                    if ($semester = $get('filter_semester')) {
                        $results = $results->filter(fn(\App\Models\RiwayatPendidikan $item) => $item->getSemester() == $semester);
                    }

                    return $results
                        ->take(20)
                        ->mapWithKeys(fn(\App\Models\RiwayatPendidikan $item) => [
                            $item->id => $item->siswa?->nama . ' (Sem ' . $item->getSemester() . ')' ?? '-'
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(
                    fn($value) =>
                    RiwayatPendidikan::with('siswa')->find($value)?->siswa?->nama ?? '-'
                ),

            // TextInput::make('semester')
            //     ->numeric()
            //     ->minValue(1)
            //     ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('akademikKrs.riwayatPendidikan.siswa.nama')
                    ->label('Nama Siswa/Mahasiswa')
                    ->searchable(),
                TextColumn::make('akademikKrs.riwayatPendidikan.tanggal_mulai')
                    ->label('Semester Saat Ini')
                    ->formatStateUsing(fn($record) => $record->akademikKrs?->riwayatPendidikan?->getSemester()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Siswa/Mahasiswa')

                    ->using(function (array $data, RelationManager $livewire) {

                        Log::info('CreateAction raw data', $data);

                        $jurusan = $data['riwayat_pendidikan_ids'] ?? [];

                        if (empty($jurusan)) {
                            Log::warning('Tidak ada mata pelajaran dipilih');
                            return null;
                        }

                        $kelas = $livewire->getOwnerRecord();

                        foreach ($jurusan as $idRiwayat) {
                            // Find active KRS for this student 
                            $activeKrs = AkademikKrs::where('id_riwayat_pendidikan', $idRiwayat)
                                ->where('status_aktif', 'Y')
                                ->latest()
                                ->first();

                            if (!$activeKrs) {
                                Log::warning("Mahasiswa (Riwayat ID: {$idRiwayat}) tidak memiliki KRS aktif.");
                                continue;
                            }

                            // Update KRS to associate with this class
                            // $activeKrs->update(['id_kelas' => $kelas->id]);

                            // Create LJK for all Mata Pelajaran in this class
                            $mapelKelasIds = $kelas->mataPelajaranKelas()->pluck('id');
                            foreach ($mapelKelasIds as $idMapel) {
                                $existing = SiswaDataLJK::where('id_akademik_krs', $activeKrs->id)
                                    ->where('id_mata_pelajaran_kelas', $idMapel)
                                    ->first();

                                // Jika sudah ada dan statusnya TL, hapus dulu agar bisa buat baru (reset)
                                if ($existing && $existing->Status_Nilai === 'TL') {
                                    $existing->delete();
                                }

                                SiswaDataLJK::firstOrCreate([
                                    'id_akademik_krs' => $activeKrs->id,
                                    'id_mata_pelajaran_kelas' => $idMapel,
                                ]);
                            }
                        }

                        return null; // hentikan default create
                    }),
            ])
            ->actions([
                DeleteAction::make()
                    ->label('Hapus dari Kelas')
                    ->modalHeading('Hapus Siswa dari Kelas')
                    ->modalDescription('Apakah Anda yakin ingin menghapus siswa ini dari kelas? Ini akan menghapus seluruh data LJK siswa untuk semua mata pelajaran di kelas ini.')
                    ->using(function (SiswaDataLJK $record, RelationManager $livewire) {
                        $kelasId = $livewire->getOwnerRecord()->id;
                        $krsId = $record->id_akademik_krs;

                        // Hapus SEMUA LJK milik mahasiswa ini yang ada di mata pelajaran kelas ini
                        SiswaDataLJK::where('id_akademik_krs', $krsId)
                            ->whereHas('mataPelajaranKelas', function ($query) use ($kelasId) {
                                $query->where('id_kelas', $kelasId);
                            })
                            ->delete();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih dari Kelas')
                        ->using(function (\Illuminate\Support\Collection $records, RelationManager $livewire) {
                            $kelasId = $livewire->getOwnerRecord()->id;
                            foreach ($records as $record) {
                                SiswaDataLJK::where('id_akademik_krs', $record->id_akademik_krs)
                                    ->whereHas('mataPelajaranKelas', function ($query) use ($kelasId) {
                                        $query->where('id_kelas', $kelasId);
                                    })
                                    ->delete();
                            }
                        }),
                ]),
            ]);
    }
    // protected function canCreate(): bool
    // {
    //     return true;
    // }

    // protected function canEdit($record): bool
    // {
    //     return true;
    // }

    // protected function canDelete($record): bool
    // {
    //     return true;
    // }

    // protected function canDeleteAny(): bool
    // {
    //     return true;
    // }
    public function isReadOnly(): bool
    {
        return false;
    }
}
