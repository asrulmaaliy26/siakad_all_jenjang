<?php

namespace App\Filament\Resources\MataPelajaranKelas\RelationManagers;

use App\Models\SiswaDataLJK;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;

class TugasRelationManager extends RelationManager
{
    protected static string $relationship = 'siswaDataLjk';

    protected static ?string $title = 'Pengumpulan Tugas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(
                array_map(function ($i) {
                    return Section::make("Tugas Ke-{$i}")
                        ->schema([
                            FileUpload::make("ljk_tugas_{$i}")
                                ->label("Upload Tugas {$i}")
                                ->disk('public')
                                ->visibility('public')
                                ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $record, (string)$i))
                                ->downloadable()
                                ->openable(),
                            RichEditor::make("ctt_tugas_{$i}")
                                ->label("Catatan Tugas {$i}")
                                ->fileAttachmentsDirectory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $record, (string)$i))
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->collapsed(); // Buat collapse agar tidak terlalu panjang
                }, range(1, 12))
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai')
            ->columnToggleFormColumns(3)
            ->columns([
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                ...array_map(function ($i) {
                    return TextColumn::make("ctt_tugas_{$i}")
                        ->label("Tugas {$i}")
                        ->formatStateUsing(function ($state, SiswaDataLJK $record) use ($i) {
                            return \App\Helpers\UjianHelper::hasSubmission($record->{"ljk_tugas_{$i}"}, $record->{"ctt_tugas_{$i}"})
                                ? 'Lihat'
                                : '-';
                        })
                        ->icon(function ($state, SiswaDataLJK $record) use ($i) {
                            return \App\Helpers\UjianHelper::hasSubmission($record->{"ljk_tugas_{$i}"}, $record->{"ctt_tugas_{$i}"})
                                ? 'heroicon-o-check-circle'
                                : 'heroicon-o-x-circle';
                        })
                        ->color(function ($state, SiswaDataLJK $record) use ($i) {
                            return \App\Helpers\UjianHelper::hasSubmission($record->{"ljk_tugas_{$i}"}, $record->{"ctt_tugas_{$i}"})
                                ? 'success'
                                : 'danger';
                        })
                        ->toggleable(isToggledHiddenByDefault: $i > 3)
                        ->action(
                            Action::make("view_tugas_{$i}")
                                ->modalHeading("Detail Tugas Ke-{$i}")
                                ->modalContent(fn(SiswaDataLJK $record) => view('filament.resources.mata-pelajaran-kelas.ljk-view', [
                                    'url' => $record->{"ljk_tugas_{$i}"} ? asset('storage/' . $record->{"ljk_tugas_{$i}"}) : null,
                                    'notes' => $record->{"ctt_tugas_{$i}"},
                                ]))
                                ->modalSubmitAction(false)
                                ->modalCancelAction(fn() => Action::make('close')->label('Tutup')->close())
                                ->closeModalByClickingAway(false)
                                ->modalWidth('7xl')
                        );
                }, range(1, 12))
            ])
            ->filters([])
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                if ($user && $user->isMurid()) {
                    // Murid hanya melihat data LJK miliknya
                    $query->whereHas('akademikKrs.riwayatPendidikan.siswaData', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            })
            ->headerActions([])
            ->actions([
                EditAction::make()
                    ->label('Upload')
                    ->visible(function (SiswaDataLJK $record) {
                        $user = auth()->user();

                        // Admin/pengajar bisa edit semua
                        if (! $user || ! $user->isMurid()) {
                            return true;
                        }

                        // Murid hanya bisa upload miliknya sendiri
                        return $record->akademikKrs?->riwayatPendidikan?->siswaData?->user_id === $user->id;
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ]);
    }
}
