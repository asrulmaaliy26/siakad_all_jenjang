<?php

namespace App\Filament\Resources\Kelas\RelationManagers;

use App\Models\AkademikKrs;
use App\Models\JenjangPendidikan;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
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

class SiswaKelasRelationManager extends RelationManager
{
    protected static string $relationship = 'AkademikKrs';
    protected static ?string $title = 'Siswa/Mahasiswa';

    public function form(Schema $form): Schema
    {
        return $form->schema([
            /* =========================
             * PILIH JURUSAN DULU
             * ========================= */
            // Select::make('id_jenjang_pendidikan')
            //     ->label('Jenjang Pendidikan')
            //     ->options(JenjangPendidikan::pluck('nama', 'id'))
            //     ->searchable()
            //     ->required()
            //     ->reactive()
            //     ->afterStateUpdated(fn($set) => $set('riwayat_pendidikan_ids', [])),

            /* =========================
             * PILIH JURUSAN DULU
             * ========================= */
            Select::make('id_jurusan')
                ->label('Jurusan')
                ->options(Jurusan::pluck('nama', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($set) => $set('riwayat_pendidikan_ids', [])),

            /* =========================
             * FILTER SEMESTER
             * ========================= */
            Select::make('filter_semester')
                ->label('Semester Saat Ini')
                ->options(array_combine(range(1, 14), range(1, 14)))
                ->searchable()
                ->reactive()
                ->afterStateUpdated(fn($set) => $set('riwayat_pendidikan_ids', [])),

            // /* =========================
            //  * MULTISELECT MAPEL (ASYNC)
            //  * ========================= */
            MultiSelect::make('riwayat_pendidikan_ids')
                ->label('Siswa/Mahasiswa')
                ->required()
                ->searchable()
                ->preload(false)
                ->optionsLimit(20)
                ->reactive()

                // Saat dropdown dibuka (tanpa search)
                ->options(function (callable $get, RelationManager $livewire, $state) {
                    $kelas = $livewire->getOwnerRecord();
                    // Ensure relation is loaded
                    $kelas->loadMissing('jurusan');
                    $jenjangId = $kelas->jurusan?->id_jenjang_pendidikan;

                    $query = RiwayatPendidikan::query()
                        ->with('siswa');

                    if ($jenjangId) {
                        $query->whereHas('jurusan', function ($q) use ($jenjangId) {
                            $q->where('id_jenjang_pendidikan', $jenjangId);
                        });
                    }

                    if ($get('id_jurusan')) {
                        $query->where('id_jurusan', $get('id_jurusan'));
                    }

                    // Filter Semester logic
                    if ($get('filter_semester')) {
                        $query->whereNotNull('tanggal_mulai');
                    }

                    $results = $query->limit(100)->get();

                    if ($semester = $get('filter_semester')) {
                        $results = $results->filter(fn($item) => $item->getSemester() == $semester);
                    }

                    return $results
                        ->take(20)
                        ->mapWithKeys(fn($item) => [
                            $item->id => $item->siswa?->nama . ' (Sem ' . $item->getSemester() . ')' ?? '-'
                        ])
                        ->toArray();
                })


                // Saat search
                ->getSearchResultsUsing(function (string $search, callable $get, RelationManager $livewire) {
                    $kelas = $livewire->getOwnerRecord();
                    $kelas->loadMissing('jurusan');
                    $jenjangId = $kelas->jurusan?->id_jenjang_pendidikan;

                    $query = RiwayatPendidikan::query()
                        ->with('siswa')
                        ->whereHas('siswa', function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%");
                        });

                    if ($jenjangId) {
                        $query->whereHas('jurusan', function ($q) use ($jenjangId) {
                            $q->where('id_jenjang_pendidikan', $jenjangId);
                        });
                    }

                    if ($get('id_jurusan')) {
                        $query->where('id_jurusan', $get('id_jurusan'));
                    }

                    if ($get('filter_semester')) {
                        $query->whereNotNull('tanggal_mulai');
                    }

                    $results = $query->limit(100)->get();

                    if ($semester = $get('filter_semester')) {
                        $results = $results->filter(fn($item) => $item->getSemester() == $semester);
                    }

                    return $results
                        ->take(20)
                        ->mapWithKeys(fn($item) => [
                            $item->id => $item->siswa?->nama . ' (Sem ' . $item->getSemester() . ')' ?? '-'
                        ])
                        ->toArray();
                })

                ->getOptionLabelUsing(
                    fn($value) =>
                    RiwayatPendidikan::find($value)?->nama
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
                TextColumn::make('riwayatPendidikan.siswa.nama')
                    ->label('Nama Siswa/Mahasiswa')
                    ->searchable(),
                TextColumn::make('riwayatPendidikan.tanggal_mulai')
                    ->label('Semester Saat Ini')
                    ->formatStateUsing(fn($record) => $record->riwayatPendidikan?->getSemester()),
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

                        foreach ($jurusan as $idjurusan) {
                            // Hitung IPS dari LJK data terakhir mahasiswa
                            $latestKrs = AkademikKrs::where('id_riwayat_pendidikan', $idjurusan)
                                ->where('status_aktif', 'Y')
                                ->latest()
                                ->first()
                                ?? AkademikKrs::where('id_riwayat_pendidikan', $idjurusan)
                                ->latest()
                                ->first();

                            $ips = 0;
                            if ($latestKrs) {
                                $ips = \App\Models\SiswaDataLJK::where('id_akademik_krs', $latestKrs->id)
                                    ->avg('Nilai_Akhir') ?? 0;
                            }

                            // Tentukan jumlah SKS berdasarkan IPS
                            // Aturan: 3 -> 24 SKS, 2 -> 18 SKS, <2 -> 12 SKS
                            $sks = 12;
                            if ($ips >= 3.0) {
                                $sks = 24;
                            } elseif ($ips >= 2.0) {
                                $sks = 18;
                            }

                            // Nonaktifkan semua KRS lama untuk riwayat pendidikan ini
                            AkademikKrs::where('id_riwayat_pendidikan', $idjurusan)
                                ->update(['status_aktif' => 'N']);

                            $kelas->akademikKrs()->create([
                                'id_riwayat_pendidikan' => $idjurusan,
                                'jumlah_sks' => $sks,
                                'tgl_krs' => now(),
                                'status_aktif' => 'Y',
                                'kode_tahun' => $kelas->tahunAkademik?->nama,
                                'status_bayar' => 'N',
                                'syarat_uts' => 'N',
                                'syarat_uas' => 'N',
                                'syarat_krs' => 'N',
                            ]);
                        }

                        return null; // hentikan default create
                    }),
            ])
            ->actions([
                DeleteAction::make(),
                // DeleteAction::make()
                //     ->disabled(fn($record) => $record->pertemuanKelas()->exists())
                //     ->tooltip('Masih memiliki data pertemuan')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

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
