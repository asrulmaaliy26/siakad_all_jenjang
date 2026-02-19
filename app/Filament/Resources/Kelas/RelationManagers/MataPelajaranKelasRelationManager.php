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

class MataPelajaranKelasRelationManager extends RelationManager
{
    protected static string $relationship = 'mataPelajaranKelas';
    protected static ?string $title = 'Mata Pelajaran Kelas';

    public function form(Schema $form): Schema
    {
        return $form->schema([
            /* =========================
             * PILIH JURUSAN DULU
             * ========================= */
            Select::make('id_kurikulum')
                ->label('kurikulum')
                ->options(Kurikulum::pluck('nama', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($set) => $set('mata_pelajaran_kurikulum_ids', [])),

            /* =========================
             * MULTISELECT MAPEL (ASYNC)
             * ========================= */
            MultiSelect::make('mata_pelajaran_kurikulum_ids')
                ->label('Mata Pelajaran')
                ->required()
                ->searchable()
                ->preload(false)
                ->optionsLimit(20)
                ->reactive()

                // Saat dropdown dibuka (tanpa search)
                ->options(function (callable $get) {
                    $query = MataPelajaranKurikulum::query()
                        ->with('mataPelajaranMaster');

                    if ($get('id_kurikulum')) {
                        $query->where('id_kurikulum', $get('id_kurikulum'));
                    }

                    return $query
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn($item) => [
                            $item->id => $item->mataPelajaranMaster->nama
                        ])
                        ->toArray();
                })


                // Saat search
                ->getSearchResultsUsing(function (string $search, callable $get) {
                    $query = MataPelajaranKurikulum::query()
                        ->with('mataPelajaranMaster')
                        ->whereHas('mataPelajaranMaster', function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%");
                        });

                    if ($get('id_kurikulum')) {
                        $query->where('id_kurikulum', $get('id_kurikulum'));
                    }

                    return $query
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn($item) => [
                            $item->id => $item->mataPelajaranMaster->nama
                        ])
                        ->toArray();
                })


                ->getOptionLabelUsing(
                    fn($value) =>
                    MataPelajaranKurikulum::find($value)?->nama
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
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if ($user && $user->hasRole('pengajar') && !$user->hasAnyRole(['super_admin', 'admin'])) {
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

                // TextColumn::make('semester'),
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
                // DeleteAction::make(),
                DeleteAction::make()
                    ->disabled(fn($record) => $record->pertemuanKelas()->exists())
                    ->tooltip('Masih memiliki data pertemuan')
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
