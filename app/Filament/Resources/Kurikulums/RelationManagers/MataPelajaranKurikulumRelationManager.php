<?php

namespace App\Filament\Resources\Kurikulums\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Actions\Action as FormAction;
use App\Models\MataPelajaranMaster;
use App\Models\Jurusan;

class MataPelajaranKurikulumRelationManager extends RelationManager
{
    protected static string $relationship = 'mataPelajaranKurikulum';
    protected static ?string $title = 'Mata Pelajaran Kurikulum';

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('id_jurusan')
                ->label('Jurusan')
                ->options(Jurusan::pluck('nama', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->default(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->id_jurusan)
                ->afterStateUpdated(fn($set) => $set('mata_pelajaran_master_ids', []))
                ->columnSpanFull(),

            TextInput::make('semester')
                ->label('Semester')
                ->helperText('Akan diterapkan ke semua mata pelajaran yang dipilih di bawah')
                ->numeric()
                ->default(0)
                ->required()
                ->columnSpanFull(),

            /* =========================
             * PILIH MAPEL (Menggunakan Select Multiple agar mendukung data besar)
             * ========================= */
            Select::make('mata_pelajaran_master_ids')
                ->label('Pilih Mata Pelajaran')
                ->multiple()
                ->searchable()
                ->preload()
                ->required()
                ->options(function (callable $get, RelationManager $livewire) {
                    $idJurusan = $get('id_jurusan');
                    if (!$idJurusan) return [];

                    // Ambil ID mapel yang sudah ada di kurikulum ini
                    $existingIds = $livewire->getOwnerRecord()
                        ->mataPelajaranKurikulum()
                        ->pluck('id_mata_pelajaran_master')
                        ->toArray();

                    return MataPelajaranMaster::where('id_jurusan', $idJurusan)
                        ->whereNotIn('id', $existingIds)
                        ->orderBy('nama')
                        ->pluck('nama', 'id');
                })
                ->getSearchResultsUsing(function (string $search, callable $get, RelationManager $livewire) {
                    $idJurusan = $get('id_jurusan');

                    $existingIds = $livewire->getOwnerRecord()
                        ->mataPelajaranKurikulum()
                        ->pluck('id_mata_pelajaran_master')
                        ->toArray();

                    return MataPelajaranMaster::where('id_jurusan', $idJurusan)
                        ->whereNotIn('id', $existingIds)
                        ->where('nama', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('nama', 'id');
                })
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mataPelajaranMaster.kode_feeder')
                    ->label('Kode Feeder')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextInputColumn::make('semester')
                    ->rules(['required', 'numeric', 'min:1'])
                    ->sortable(),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('semester')
                    ->options([
                        '1' => 'Semester 1',
                        '2' => 'Semester 2',
                        '3' => 'Semester 3',
                        '4' => 'Semester 4',
                        '5' => 'Semester 5',
                        '6' => 'Semester 6',
                        '7' => 'Semester 7',
                        '8' => 'Semester 8',
                    ])
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
                EditAction::make(),
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
