<?php

namespace App\Filament\Resources\MataPelajaranKelas\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class JurnalPengajaranRelationManager extends RelationManager
{
    protected static string $relationship = 'jurnalPengajaran';

    protected static ?string $title = 'Jurnal Pengajaran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul Jurnal')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('deadline')
                    ->label('Deadline'),

                Forms\Components\Select::make('status_akses')
                    ->options([
                        'Y' => 'Publik',
                        'N' => 'Private',
                    ])
                    ->required()
                    ->default('N'),

                Forms\Components\Select::make('dokumen')
                    ->multiple()
                    ->relationship('dokumen', 'judul_dokumen', function (Builder $query) {
                        // Filter documents: only show those belonging to the Dosen of this Class
                        return $query->where('id_dosen', $this->getOwnerRecord()->id_dosen_data);
                    })
                    ->preload()
                    ->label('Dokumen Terkait (Materi/Tugas Dosen)'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dokumen')
                    ->label('Dokumen')
                    ->formatStateUsing(function ($record) {
                        return $record->dokumen->map(
                            fn($doc) =>
                            '<a href="' . asset('storage/' . $doc->file_path) . '" target="_blank" class="text-primary-600 underline hover:text-primary-500" title="' . e($doc->judul_dokumen) . '">' . \Illuminate\Support\Str::limit(e($doc->judul_dokumen), 20) . '</a>'
                        )->implode('<br>');
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('mataPelajaranKelas.dosenData.nama')
                    ->label('Dosen')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable(),

                Tables\Columns\SelectColumn::make('status_akses')
                    ->label('Akses')
                    ->options([
                        'Y' => 'Publik',
                        'N' => 'Private',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),

                Tables\Columns\SelectColumn::make('type')
                    ->label('Tipe')
                    ->options([
                        'Materi' => 'Materi',
                        'Tugas' => 'Tugas',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->before(function (CreateAction $action, array $data) {
                        if (($data['type'] ?? '') === 'tugas') {
                            $count = \App\Models\JurnalPengajaran::where('id_mata_pelajaran_kelas', $this->getOwnerRecord()->id)
                                ->whereIn('type', ['tugas', 'Tugas'])
                                ->count();
                            if ($count >= 3) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Maksimal 3 Tugas')
                                    ->body('Mata pelajaran ini sudah memiliki 3 jurnal bertipe tugas. Tidak bisa menambah lagi.')
                                    ->danger()
                                    ->send();
                                $action->halt();
                            }
                        }
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->before(function (EditAction $action, array $data, $record) {
                        // Check if changing TO 'tugas' from something else
                        $newType = $data['type'] ?? $record->type;
                        $oldType = $record->type;

                        $isNewTugas = in_array(strtolower($newType), ['tugas']);
                        $wasTugas = in_array(strtolower($oldType), ['tugas']);

                        if ($isNewTugas && !$wasTugas) {
                            $count = \App\Models\JurnalPengajaran::where('id_mata_pelajaran_kelas', $this->getOwnerRecord()->id)
                                ->whereIn('type', ['tugas', 'Tugas'])
                                ->count();

                            if ($count >= 3) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Maksimal 3 Tugas')
                                    ->body('Mata pelajaran ini sudah memiliki 3 jurnal bertipe tugas. Tidak bisa mengubah jurnal ini menjadi tugas.')
                                    ->danger()
                                    ->send();
                                $action->halt();
                            }
                        }
                    }),
                DeleteAction::make(),
                ViewAction::make(),
                Action::make('view_dokumen_tugas')
                    ->label('Detail Jurnal')
                    ->icon('heroicon-o-eye')
                    ->visible(fn($record) => $record->status_akses !== 'N')
                    ->modalHeading(fn($record) => ($record->type === 'tugas' || $record->type === 'Tugas') ? 'Detail Materi & Pengumpulan Tugas' : 'Detail Materi')
                    ->modalWidth('4xl')
                    ->form(function ($record) {
                        $components = [];

                        // 1. Document List
                        if ($record->dokumen->count() > 0) {
                            $docList = '<ul class="space-y-3 border border-gray-200 dark:border-gray-700 p-4 rounded-lg bg-gray-50 dark:bg-gray-800">' .
                                $record->dokumen->map(
                                    fn($doc) =>
                                    '<li><a href="' . asset('storage/' . $doc->file_path) . '" target="_blank" class="flex items-center gap-3 text-primary-600 hover:text-primary-500 transition-colors group">' .
                                        '<div class="p-2 bg-white dark:bg-gray-700 rounded-md shadow-sm group-hover:shadow-md transition-shadow"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>' .
                                        '<span class="font-medium underline decoration-transparent group-hover:decoration-primary-500 transition-all">' . e($doc->judul_dokumen) . '</span></a></li>'
                                )->implode('') .
                                '</ul>';

                            $components[] = Forms\Components\Placeholder::make('dokumen_list')
                                ->label('Dokumen Lampiran')
                                ->content(new \Illuminate\Support\HtmlString($docList));
                        } else {
                            $components[] = Forms\Components\Placeholder::make('dokumen_list')
                                ->label('Dokumen Lampiran')
                                ->content(new \Illuminate\Support\HtmlString('<span class="text-gray-500 italic">Tidak ada dokumen lampiran.</span>'));
                        }

                        // 2. Tugas Management (only if type is tugas)
                        if ($record->type === 'tugas' || $record->type === 'Tugas') {
                            // Determine task index
                            $taskIndex = \App\Models\JurnalPengajaran::where('id_mata_pelajaran_kelas', $record->id_mata_pelajaran_kelas)
                                ->whereIn('type', ['tugas', 'Tugas'])
                                ->where('id', '<=', $record->id)
                                ->orderBy('id')
                                ->count();

                            $ljkField = "ljk_tugas_{$taskIndex}";
                            $cttField = "ctt_tugas_{$taskIndex}";

                            $components[] = Section::make('Pengumpulan Tugas Mahasiswa')
                                ->description('Pilih mahasiswa untuk melihat atau mengunggah tugas.')
                                ->icon('heroicon-o-clipboard-document-check')
                                ->schema([
                                    Forms\Components\Select::make('student_id')
                                        ->label('Pilih Mahasiswa')
                                        ->options(function () use ($record) {
                                            $user = auth()->user();
                                            // Ensure permissions are correctly loaded
                                            if ($user && $user->can('view_any_mata_pelajaran_kelas')) { // Or check role 'murid'
                                                // Wait, best check is role
                                            }

                                            $query = \App\Models\SiswaDataLJK::where('id_mata_pelajaran_kelas', $record->id_mata_pelajaran_kelas)
                                                ->with('akademikKrs.riwayatPendidikan.siswaData');

                                            if ($user && $user->hasRole('murid')) {
                                                $query->whereHas('akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
                                                    $q->where('user_id', $user->id);
                                                });
                                            }

                                            return $query->get()
                                                ->mapWithKeys(function ($ljk) {
                                                    $nama = $ljk->akademikKrs->riwayatPendidikan->siswaData->nama ?? 'Unknown';
                                                    return [$ljk->id => $nama];
                                                });
                                        })
                                        ->default(function () use ($record) {
                                            $user = auth()->user();
                                            if ($user && $user->hasRole('murid')) {
                                                return \App\Models\SiswaDataLJK::where('id_mata_pelajaran_kelas', $record->id_mata_pelajaran_kelas)
                                                    ->whereHas('akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
                                                        $q->where('user_id', $user->id);
                                                    })->value('id');
                                            }
                                            return null;
                                        })
                                        ->disabled(fn() => auth()->user() && auth()->user()->hasRole('murid'))
                                        ->dehydrated() // Ensure value is passed even if disabled
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set) use ($ljkField, $cttField) {
                                            if ($state) {
                                                $ljk = \App\Models\SiswaDataLJK::find($state);
                                                if ($ljk) {
                                                    $set($ljkField, $ljk->$ljkField);
                                                    $set($cttField, $ljk->$cttField);
                                                }
                                            }
                                        }),
                                    Grid::make(1)->schema([
                                        Forms\Components\FileUpload::make($ljkField)
                                            ->label('File Tugas')
                                            ->disk('public')
                                            ->directory(function ($get) {
                                                $ljkId = $get('student_id');
                                                $ljkRecord = $ljkId ? \App\Models\SiswaDataLJK::find($ljkId) : null;
                                                return \App\Helpers\UploadPathHelper::uploadUjianPath($get, $ljkRecord, 'tugas');
                                            })
                                            ->downloadable()
                                            ->openable()
                                            ->columnSpan(1)
                                            ->default(function () use ($record, $ljkField) {
                                                $user = auth()->user();
                                                if ($user && $user->hasRole('murid')) {
                                                    return \App\Models\SiswaDataLJK::where('id_mata_pelajaran_kelas', $record->id_mata_pelajaran_kelas)
                                                        ->whereHas('akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
                                                            $q->where('user_id', $user->id);
                                                        })->value($ljkField);
                                                }
                                                return null;
                                            })
                                            ->visible(fn($get) => filled($get('student_id'))),
                                        Forms\Components\RichEditor::make($cttField)
                                            ->label('Catatan Tugas')
                                            ->columnSpan(1)
                                            ->default(function () use ($record, $cttField) {
                                                $user = auth()->user();
                                                if ($user && $user->hasRole('murid')) {
                                                    return \App\Models\SiswaDataLJK::where('id_mata_pelajaran_kelas', $record->id_mata_pelajaran_kelas)
                                                        ->whereHas('akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
                                                            $q->where('user_id', $user->id);
                                                        })->value($cttField);
                                                }
                                                return null;
                                            })
                                            ->visible(fn($get) => filled($get('student_id'))),
                                    ])->visible(fn($get) => filled($get('student_id')))
                                ]);
                        }

                        return $components;
                    })
                    ->action(function (array $data, $record) {
                        if (($record->type === 'tugas' || $record->type === 'Tugas') && !empty($data['student_id'])) {
                            $ljk = \App\Models\SiswaDataLJK::find($data['student_id']);
                            if ($ljk) {
                                // Determine task index
                                $taskIndex = \App\Models\JurnalPengajaran::where('id_mata_pelajaran_kelas', $record->id_mata_pelajaran_kelas)
                                    ->whereIn('type', ['tugas', 'Tugas'])
                                    ->where('id', '<=', $record->id)
                                    ->orderBy('id')
                                    ->count();

                                $ljkField = "ljk_tugas_{$taskIndex}";
                                $cttField = "ctt_tugas_{$taskIndex}";

                                $ljk->update([
                                    $ljkField => $data[$ljkField] ?? null,
                                    $cttField => $data[$cttField] ?? null,
                                    'tgl_upload_tugas' => now(),
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Tugas berhasil disimpan')
                                    ->success()
                                    ->send();
                            }
                        }
                    })
                    ->modalSubmitAction(fn($record) => ($record->type === 'tugas' || $record->type === 'Tugas') ? null : false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
