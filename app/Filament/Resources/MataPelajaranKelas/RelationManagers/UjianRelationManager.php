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
                            ->multiple()
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
                            ->fileAttachmentsDirectory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'ljk_uts'))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                Section::make('Ujian Akhir Semester (UAS)')
                    ->schema([
                        FileUpload::make('ljk_uas')
                            ->label('Upload LJK UAS')
                            ->disk('public')
                            ->visibility('public')
                            ->multiple()
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
                            ->fileAttachmentsDirectory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'ljk_uas'))
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

                TextColumn::make('cekal_kuliah')
                    ->label('Status Cekal')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'Dicekal' : ($state === 'N' ? 'Aman' : 'Belum Diset'))
                    ->color(fn($state) => $state === 'Y' ? 'danger' : 'success')
                    ->visible(fn() => ! auth()->user()?->isMurid())
                    ->action(
                        Action::make('edit_cekal_col')
                            ->modalHeading('Ubah Status Cekal')
                            ->form([
                                \Filament\Forms\Components\Select::make('cekal_kuliah')
                                    ->label('Status Cekal Ujian')
                                    ->options([
                                        'Y' => '🔴 Dicekal (Blokir Ujian)',
                                        'N' => '🟢 Aman (Boleh Ujian)',
                                    ])
                                    ->default(fn(SiswaDataLJK $record) => $record->cekal_kuliah ?? 'N')
                                    ->required(),
                            ])
                            ->action(function (SiswaDataLJK $record, array $data): void {
                                $record->update(['cekal_kuliah' => $data['cekal_kuliah']]);
                                \Filament\Notifications\Notification::make()
                                    ->title('Status cekal berhasil diperbarui')
                                    ->success()
                                    ->send();
                            })
                    ),

                // Kolom LJK UTS (gabung dengan ctt_uts)
                TextColumn::make('ctt_uts')
                    ->label('LJK UTS')
                    ->formatStateUsing(function ($state, SiswaDataLJK $record) {
                        $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uts');
                        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                        return \App\Helpers\UjianHelper::hasSubmission($files, $record->ctt_uts)
                            ? 'Lihat Jawaban'
                            : '-';
                    })
                    ->icon(function ($state, SiswaDataLJK $record) {
                        $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uts');
                        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                        return \App\Helpers\UjianHelper::hasSubmission($files, $record->ctt_uts)
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-x-circle';
                    })
                    ->color(function ($state, SiswaDataLJK $record) {
                        $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uts');
                        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                        return \App\Helpers\UjianHelper::hasSubmission($files, $record->ctt_uts)
                            ? 'success'
                            : 'danger';
                    })
                    ->action(
                        Action::make('view_uts')
                            ->modalHeading('Detail LJK UTS')
                            ->modalContent(function(SiswaDataLJK $record) {
                                $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uts');
                                $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                                return view('filament.resources.mata-pelajaran-kelas.ljk-view', [
                                    'files' => $files,
                                    'notes' => $record->ctt_uts,
                                ]);
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelAction(fn() => Action::make('close')->label('Tutup')->close())
                            ->closeModalByClickingAway(false)
                            ->modalWidth('7xl')
                    ),

                // Kolom LJK UAS (gabung dengan ctt_uas)
                TextColumn::make('ctt_uas')
                    ->label('LJK UAS')
                    ->formatStateUsing(function ($state, SiswaDataLJK $record) {
                        $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uas');
                        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                        return \App\Helpers\UjianHelper::hasSubmission($files, $record->ctt_uas)
                            ? 'Lihat Jawaban'
                            : '-';
                    })
                    ->icon(function ($state, SiswaDataLJK $record) {
                        $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uas');
                        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                        return \App\Helpers\UjianHelper::hasSubmission($files, $record->ctt_uas)
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-x-circle';
                    })
                    ->color(function ($state, SiswaDataLJK $record) {
                        $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uas');
                        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                        return \App\Helpers\UjianHelper::hasSubmission($files, $record->ctt_uas)
                            ? 'success'
                            : 'danger';
                    })
                    ->action(
                        Action::make('view_uas')
                            ->modalHeading('Detail LJK UAS')
                            ->modalContent(function(SiswaDataLJK $record) {
                                $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $record, 'ljk_uas');
                                $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                                return view('filament.resources.mata-pelajaran-kelas.ljk-view', [
                                    'files' => $files,
                                    'notes' => $record->ctt_uas,
                                ]);
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelAction(fn() => Action::make('close')->label('Tutup')->close())
                            ->closeModalByClickingAway(false)
                            ->modalWidth('7xl')
                    ),
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
                Action::make('edit_cekal')
                    ->label('Ubah Cekal')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('warning')
                    ->visible(fn() => ! auth()->user()?->isMurid())
                    ->form([
                        \Filament\Forms\Components\Select::make('cekal_kuliah')
                            ->label('Status Cekal Ujian')
                            ->options([
                                'Y' => '🔴 Dicekal (Blokir Ujian)',
                                'N' => '🟢 Aman (Boleh Ujian)',
                            ])
                            ->default(fn(SiswaDataLJK $record) => $record->cekal_kuliah ?? 'N')
                            ->required(),
                    ])
                    ->action(function (SiswaDataLJK $record, array $data): void {
                        $record->update(['cekal_kuliah' => $data['cekal_kuliah']]);
                        \Filament\Notifications\Notification::make()
                            ->title('Status cekal berhasil diperbarui')
                            ->success()
                            ->send();
                    }),
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
