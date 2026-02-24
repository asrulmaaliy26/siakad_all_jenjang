<?php

namespace App\Filament\Resources\Kelas\RelationManagers;

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
use App\Models\MataPelajaranMaster;
use App\Models\Jurusan;
use App\Models\Kurikulum;
use App\Models\MataPelajaranKurikulum;
use Illuminate\Database\Eloquent\Builder;

class MataPelajaranKelasRelationManager extends RelationManager
{
    protected static string $relationship = 'mataPelajaranKelas';
    protected static ?string $title = 'Mata Pelajaran Kelas';

    public function form(Schema $form): Schema
    {
        return $form->schema([
            /* =========================
             * FILTER JURUSAN (Default ke Jurusan Kelas)
             * ========================= */
            Select::make('id_jurusan')
                ->label('Jurusan')
                ->options(Jurusan::pluck('nama', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->default(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->id_jurusan)
                ->afterStateUpdated(fn($set) => $set('mata_pelajaran_kurikulum_ids', [])),

            /* =========================
             * FILTER KURIKULUM (Optional, filter by Jurusan)
             * ========================= */
            Select::make('id_kurikulum')
                ->label('Kurikulum')
                ->options(function (callable $get) {
                    $jurusanId = $get('id_jurusan');
                    if (!$jurusanId) return Kurikulum::pluck('nama', 'id');
                    return Kurikulum::where('id_jurusan', $jurusanId)->pluck('nama', 'id');
                })
                ->searchable()
                ->reactive()
                ->afterStateUpdated(fn($set) => $set('mata_pelajaran_kurikulum_ids', [])),

            /* =========================
             * FILTER SEMESTER (Default ke Semester Kelas)
             * ========================= */
            Select::make('semester')
                ->label('Filter Semester Mapel')
                ->options([
                    1 => 'Semester 1',
                    2 => 'Semester 2',
                    3 => 'Semester 3',
                    4 => 'Semester 4',
                    5 => 'Semester 5',
                    6 => 'Semester 6',
                    7 => 'Semester 7',
                    8 => 'Semester 8',
                ])
                ->reactive()
                ->default(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->semester)
                ->afterStateUpdated(fn($set) => $set('mata_pelajaran_kurikulum_ids', [])),

            /* =========================
             * MULTISELECT MAPEL KURIKULUM
             * ========================= */
            MultiSelect::make('mata_pelajaran_kurikulum_ids')
                ->label('Mata Pelajaran')
                ->required()
                ->searchable()
                ->preload(false)
                ->reactive()

                // Saat dropdown dibuka (tanpa search)
                ->options(function (callable $get, RelationManager $livewire) {
                    $jurusanId = $get('id_jurusan');
                    $kurikulumId = $get('id_kurikulum');
                    $semester = $get('semester');

                    $existingIds = $livewire->getOwnerRecord()
                        ->mataPelajaranKelas()
                        ->pluck('id_mata_pelajaran_kurikulum')
                        ->toArray();

                    $query = MataPelajaranKurikulum::query()
                        ->with('mataPelajaranMaster')
                        ->whereNotIn('id', $existingIds);

                    if ($kurikulumId) {
                        $query->where('id_kurikulum', $kurikulumId);
                    } elseif ($jurusanId) {
                        $query->whereHas('kurikulum', fn($q) => $q->where('id_jurusan', $jurusanId));
                    }

                    if ($semester) {
                        $query->where('semester', $semester);
                    }

                    return $query
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn($item) => [
                            $item->id => ($item->mataPelajaranMaster->nama ?? 'N/A') . ' - Semester ' . $item->semester
                        ])
                        ->toArray();
                })

                // Saat search
                ->getSearchResultsUsing(function (string $search, callable $get, RelationManager $livewire) {
                    $jurusanId = $get('id_jurusan');
                    $kurikulumId = $get('id_kurikulum');
                    $semester = $get('semester');

                    $existingIds = $livewire->getOwnerRecord()
                        ->mataPelajaranKelas()
                        ->pluck('id_mata_pelajaran_kurikulum')
                        ->toArray();

                    $query = MataPelajaranKurikulum::query()
                        ->with('mataPelajaranMaster')
                        ->whereNotIn('id', $existingIds)
                        ->whereHas('mataPelajaranMaster', function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%");
                        });

                    if ($kurikulumId) {
                        $query->where('id_kurikulum', $kurikulumId);
                    } elseif ($jurusanId) {
                        $query->whereHas('kurikulum', fn($q) => $q->where('id_jurusan', $jurusanId));
                    }

                    if ($semester) {
                        $query->where('semester', $semester);
                    }

                    return $query
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn($item) => [
                            $item->id => ($item->mataPelajaranMaster->nama ?? 'N/A') . ' - Semester ' . $item->semester
                        ])
                        ->toArray();
                })

                ->getOptionLabelUsing(function ($value) {
                    $item = MataPelajaranKurikulum::with('mataPelajaranMaster')->find($value);
                    return $item ? ($item->mataPelajaranMaster->nama ?? 'N/A') . ' - Semester ' . $item->semester : null;
                })
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if ($user && $user->isPengajar()) {
                    $query->whereHas('dosenData', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            })
            ->columns([
                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                TextColumn::make('mataPelajaranKurikulum.kurikulum.nama')
                    ->label('Kurikulum')
                    ->searchable(),

                TextColumn::make('mataPelajaranKurikulum.semester')
                    ->label('Semester'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Mata Pelajaran')

                    ->using(function (array $data, RelationManager $livewire) {

                        Log::info('CreateAction raw data', $data);

                        $mapelIds = $data['mata_pelajaran_kurikulum_ids'] ?? [];

                        if (empty($mapelIds)) {
                            Log::warning('Tidak ada mata pelajaran dipilih');
                            return null;
                        }

                        $kelas = $livewire->getOwnerRecord();

                        foreach ($mapelIds as $idMapel) {
                            $kelas->mataPelajaranKelas()->create([
                                'id_mata_pelajaran_kurikulum' => $idMapel,
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
}
