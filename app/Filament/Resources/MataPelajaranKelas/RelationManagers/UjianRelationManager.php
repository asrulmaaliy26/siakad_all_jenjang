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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class UjianRelationManager extends RelationManager
{
    protected static string $relationship = 'siswaDataLjk';

    protected static ?string $title = 'Ujian';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ujian Tengah Semester (UTS)')
                    ->schema([
                        FileUpload::make('ljk_uts')
                            ->label('Upload LJK UTS')
                            ->disk('public')
                            ->visibility('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'ljk_uts'))
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->downloadable()
                            ->openable()
                            // Hapus file saat klik ❌
                            ->afterStateUpdated(function ($state, $record) {
                                if (blank($state) && $record?->foto_profil) {
                                    Storage::disk('public')->delete($record->foto_profil);
                                }
                            })

                            // Hapus file lama saat upload baru
                            ->deleteUploadedFileUsing(function ($file, $record) {
                                if ($record?->foto_profil) {
                                    Storage::disk('public')->delete($record->foto_profil);
                                }
                                return true;
                            }), // full width,,
                        RichEditor::make('ctt_uts')
                            ->label('Catatan / Jawaban UTS')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                Section::make('Ujian Akhir Semester (UAS)')
                    ->schema([
                        FileUpload::make('ljk_uas')
                            ->label('Upload LJK UAS')
                            ->disk('public')
                            ->visibility('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'ljk_uas'))
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->downloadable()
                            ->openable()
                            // Hapus file saat klik ❌
                            ->afterStateUpdated(function ($state, $record) {
                                if (blank($state) && $record?->foto_profil) {
                                    Storage::disk('public')->delete($record->foto_profil);
                                }
                            })

                            // Hapus file lama saat upload baru
                            ->deleteUploadedFileUsing(function ($file, $record) {
                                if ($record?->foto_profil) {
                                    Storage::disk('public')->delete($record->foto_profil);
                                }
                                return true;
                            }), // full width,,
                        RichEditor::make('ctt_uas')
                            ->label('Catatan / Jawaban UAS')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai')
            ->columns([
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                // Kolom LJK UTS (gabung dengan ctt_uts)
                TextColumn::make('ctt_uts')
                    ->label('LJK UTS')
                    ->formatStateUsing(function ($state, SiswaDataLJK $record) {
                        return \App\Helpers\UjianHelper::hasSubmission($record->ljk_uts, $record->ctt_uts)
                            ? 'Lihat Jawaban'
                            : '-';
                    })
                    ->icon(function ($state, SiswaDataLJK $record) {
                        return \App\Helpers\UjianHelper::hasSubmission($record->ljk_uts, $record->ctt_uts)
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-x-circle';
                    })
                    ->color(function ($state, SiswaDataLJK $record) {
                        return \App\Helpers\UjianHelper::hasSubmission($record->ljk_uts, $record->ctt_uts)
                            ? 'success'
                            : 'danger';
                    })
                    ->action(
                        Action::make('view_uts')
                            ->modalHeading('Detail LJK UTS')
                            ->modalContent(fn(SiswaDataLJK $record) => view('filament.resources.mata-pelajaran-kelas.ljk-view', [
                                'url' => $record->ljk_uts ? asset('storage/' . $record->ljk_uts) : null,
                                'notes' => $record->ctt_uts,
                            ]))
                            ->modalSubmitAction(false)
                            ->modalCancelAction(fn() => Action::make('close')->label('Tutup')->close())
                            ->closeModalByClickingAway(false)
                            ->modalWidth('7xl')
                    ),

                // Kolom LJK UAS (gabung dengan ctt_uas)
                TextColumn::make('ctt_uas')
                    ->label('LJK UAS')
                    ->formatStateUsing(function ($state, SiswaDataLJK $record) {
                        return \App\Helpers\UjianHelper::hasSubmission($record->ljk_uas, $record->ctt_uas)
                            ? 'Lihat Jawaban'
                            : '-';
                    })
                    ->icon(function ($state, SiswaDataLJK $record) {
                        return \App\Helpers\UjianHelper::hasSubmission($record->ljk_uas, $record->ctt_uas)
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-x-circle';
                    })
                    ->color(function ($state, SiswaDataLJK $record) {
                        return \App\Helpers\UjianHelper::hasSubmission($record->ljk_uas, $record->ctt_uas)
                            ? 'success'
                            : 'danger';
                    })
                    ->action(
                        Action::make('view_uas')
                            ->modalHeading('Detail LJK UAS')
                            ->modalContent(fn(SiswaDataLJK $record) => view('filament.resources.mata-pelajaran-kelas.ljk-view', [
                                'url' => $record->ljk_uas ? asset('storage/' . $record->ljk_uas) : null,
                                'notes' => $record->ctt_uas,
                            ]))
                            ->modalSubmitAction(false)
                            ->modalCancelAction(fn() => Action::make('close')->label('Tutup')->close())
                            ->closeModalByClickingAway(false)
                            ->modalWidth('7xl')
                    ),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                EditAction::make()
                    ->label('Upload'),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ]);
    }
}
