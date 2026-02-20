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
                    if ($semester = $get('filter_semester')) {
                        // Logic: (Year(Now) - Year(Start)) * 2 + (Month(Now) >= 7 ? 1 : 0) - (Month(Start) >= 7 ? 0 : 1) + 1
                        // Simplified: Every 6 months is a semester.
                        // Assuming Ganjil starts in July (7), Genap starts in January (1).

                        // We filter using whereRaw for performance, or fetch and filter in PHP if dataset is small.
                        // Given we need pagination/limit, SQL based is better but complex date math.
                        // Let's use a simpler heuristic or standard calculation if possible.
                        // "setiap tahun 2 kali dari januari ihngg dst" -> Jan-Jun = Genap/Ganjil?
                        // Usually Academic Year starts in July.
                        // Jan 2024 - Jun 2024: Sem Genap 2023/2024
                        // Jul 2024 - Dec 2024: Sem Ganjil 2024/2025

                        // User request: "count tanggal mulai jika setiap tahun 2 kali dari januari ihngg dst"
                        // This implies: 
                        // Smt 1: Starts Jan Year X  (or Jul Year X-1)
                        // This seems to imply simple 6-month blocks from Start Date.

                        // Let's implement filtering in PHP for the results found, 
                        // filtering query where valid date exists.

                        $query->whereNotNull('tanggal_mulai');

                        // Since we can't easily do complex date math in all SQL dialects for semeters query-side without stored procs or complex logic:
                        // We will filter the results after fetch if list is small, OR 
                        // use a whereRaw approximation.

                        // Let's try PHP filtering on a slightly larger result set or refine query.
                        // Actually, for a MultiSelect with search, we need to return array [id => label].
                        // We can fetch candidate rows and filter.
                    }

                    // Get results and filter in PHP
                    $results = $query->limit(100)->get(); // Fetch more to allow filtering

                    if ($semester = $get('filter_semester')) {
                        $results = $results->filter(function ($item) use ($semester) {
                            if (!$item->tanggal_mulai) return false;

                            $start = \Carbon\Carbon::parse($item->tanggal_mulai);
                            $now = \Carbon\Carbon::now();

                            // Calculate diff in months
                            $diffInMonths = $start->diffInMonths($now);

                            // Calculate semester: 0-5 months = Sem 1, 6-11 = Sem 2, etc.
                            // Formula: floor(months / 6) + 1
                            $calculatedSemester = floor($diffInMonths / 6) + 1;

                            return $calculatedSemester == $semester;
                        });
                    }

                    return $results
                        ->take(20)
                        ->mapWithKeys(fn($item) => [
                            $item->id => $item->siswa?->nama . ' (Sem ' . $this->calculateSemester($item->tanggal_mulai) . ')' ?? '-'
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
                        $results = $results->filter(function ($item) use ($semester) {
                            if (!$item->tanggal_mulai) return false;
                            $start = \Carbon\Carbon::parse($item->tanggal_mulai);
                            $now = \Carbon\Carbon::now();
                            $diffInMonths = $start->diffInMonths($now);
                            $calculatedSemester = floor($diffInMonths / 6) + 1;
                            return $calculatedSemester == $semester;
                        });
                    }

                    return $results
                        ->take(20)
                        ->mapWithKeys(fn($item) => [
                            $item->id => $item->siswa?->nama . ' (Sem ' . $this->calculateSemester($item->tanggal_mulai) . ')' ?? '-'
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
                    ->label('Semester')
                    ->formatStateUsing(fn($state) => $this->calculateSemester($state)),
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
                            $kelas->akademikKrs()->create([
                                'id_riwayat_pendidikan' => $idjurusan,
                                // 'semester' => $data['semester'],
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

    protected function calculateSemester($tanggalMulai)
    {
        if (!$tanggalMulai) return '?';
        $start = \Carbon\Carbon::parse($tanggalMulai);
        $now = \Carbon\Carbon::now();
        $diffInMonths = $start->diffInMonths($now);
        return floor($diffInMonths / 6) + 1;
    }
}
