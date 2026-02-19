<?php

namespace App\Filament\Resources\Kurikulums\RelationManagers;

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

class MataPelajaranKurikulumRelationManager extends RelationManager
{
    protected static string $relationship = 'mataPelajaranKurikulum';
    protected static ?string $title = 'Mata Pelajaran Kurikulum';

    public function form(Schema $form): Schema
    {
        return $form->schema([

            /* =========================
             * PILIH JURUSAN DULU
             * ========================= */
            Select::make('id_jurusan')
                ->label('Jurusan')
                ->options(Jurusan::pluck('nama', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($set) => $set('mata_pelajaran_master_ids', [])),

            /* =========================
             * MULTISELECT MAPEL (ASYNC)
             * ========================= */
            MultiSelect::make('mata_pelajaran_master_ids')
                ->label('Mata Pelajaran')
                ->required()
                ->searchable()
                ->preload(false)
                ->optionsLimit(20)
                ->reactive()

                // Saat dropdown dibuka (tanpa search)
                ->options(function (callable $get) {
                    $query = MataPelajaranMaster::query();

                    if ($get('id_jurusan')) {
                        $query->where('id_jurusan', $get('id_jurusan'));
                    }

                    return $query
                        ->limit(20)
                        ->pluck('nama', 'id');
                })

                // Saat search
                ->getSearchResultsUsing(function (string $search, callable $get) {
                    $query = MataPelajaranMaster::query()
                        ->where('nama', 'like', "%{$search}%");

                    if ($get('id_jurusan')) {
                        $query->where('id_jurusan', $get('id_jurusan'));
                    }

                    return $query
                        ->limit(20)
                        ->pluck('nama', 'id');
                })

                ->getOptionLabelUsing(
                    fn($value) =>
                    MataPelajaranMaster::find($value)?->nama
                ),

            TextInput::make('semester')
                ->numeric()
                ->minValue(1)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable(),

                TextColumn::make('semester'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Mata Pelajaran')

                    ->using(function (array $data, RelationManager $livewire) {

                        Log::info('CreateAction raw data', $data);

                        $mapelIds = $data['mata_pelajaran_master_ids'] ?? [];

                        if (empty($mapelIds)) {
                            Log::warning('Tidak ada mata pelajaran dipilih');
                            return null;
                        }

                        $kurikulum = $livewire->getOwnerRecord();

                        foreach ($mapelIds as $idMapel) {
                            $kurikulum->mataPelajaranKurikulum()->create([
                                'id_mata_pelajaran_master' => $idMapel,
                                'semester' => $data['semester'],
                            ]);
                        }

                        return null; // hentikan default create
                    }),
            ])
            ->actions([
                DeleteAction::make(),
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
