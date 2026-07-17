<?php

namespace App\Filament\Resources\SiswaData\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\Action;
use App\Filament\Resources\SiswaData\SiswaDataResource;
use Illuminate\Database\Eloquent\Builder;

class SiswaDataTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('No'),
                ImageColumn::make('foto_profil')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=' . urlencode('Siswa'))),
                TextColumn::make('angkatan')
                    ->label('Angkatan')
                    ->state(function ($record) {
                        // Prioritas 1: dari riwayat pendidikan aktif
                        if ($record->riwayatPendidikanAktif?->angkatan) {
                            return $record->riwayatPendidikanAktif->angkatan;
                        }
                        // Prioritas 2: dari riwayat pendidikan manapun (termasuk tidak aktif)
                        if ($record->riwayatPendidikanTerbaru?->angkatan) {
                            return $record->riwayatPendidikanTerbaru->angkatan;
                        }
                        // Prioritas 3: dari tahun daftar di data pendaftar
                        if ($record->pendaftar?->Tgl_Daftar) {
                            return date('Y', strtotime($record->pendaftar->Tgl_Daftar));
                        }
                        return '-';
                    })
                    ->toggleable(),
                TextColumn::make('riwayatPendidikanAktif.nomor_induk')
                    ->label('Nomor Induk')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('nama_lengkap')
                    ->label('Nama lengkap')
                    ->state(function ($record) {
                        // Prioritas 1: dari siswa_data.nama_lengkap
                        if (!empty($record->nama_lengkap)) return $record->nama_lengkap;
                        // Prioritas 2: dari data pendaftar
                        if (!empty($record->pendaftar?->Nama_Lengkap)) return $record->pendaftar->Nama_Lengkap;
                        // Prioritas 3: dari akun user
                        if (!empty($record->user?->name)) return $record->user->name;
                        return '(Nama belum diisi)';
                    })
                    ->searchable(query: fn(\Illuminate\Database\Eloquent\Builder $query, string $search) => $query->where('siswa_data.nama_lengkap', 'like', "%{$search}%")->orWhere('sdp_sort.Nama_Lengkap', 'like', "%{$search}%")->orWhere('usr_sort.name', 'like', "%{$search}%"))
                    ->sortable(query: fn(\Illuminate\Database\Eloquent\Builder $query, string $direction) => $query->orderByRaw('COALESCE(siswa_data.nama_lengkap, sdp_sort.Nama_Lengkap, usr_sort.name) ' . $direction))
                    ->description(fn($record) => $record->user?->email
                        ? '🔑 ' . $record->user->email
                        : '⚠️ Belum punya akun')
                    ->toggleable(),

                TextColumn::make('riwayatPendidikanAktif.programSekolah.nilai')
                    ->searchable()
                    ->label('Program Sekolah')
                    ->toggleable(),
                TextColumn::make('riwayatPendidikanAktif.jurusan.nama')
                    ->searchable()
                    ->label('Jurusan')
                    ->toggleable(),
                TextColumn::make('riwayatPendidikanAktif.statusSiswa.nilai')
                    ->label('S_Pend')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status_siswa')
                    ->label('S_Siswa')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'aktif' => 'success',
                        'tidak aktif' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'aktif' => 'Aktif',
                        'tidak aktif' => 'Tidak Aktif',
                        default => ucfirst($state ?? '-'),
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('status_siswa')
                    ->label('Status Siswa')
                    ->placeholder('Semua Siswa')
                    ->trueLabel('Siswa Aktif')
                    ->falseLabel('Siswa Tidak Aktif')
                    ->queries(
                        true: fn(Builder $query) => $query->where('status_siswa', 'aktif'),
                        false: fn(Builder $query) => $query->where(function ($q) {
                            $q->where('status_siswa', 'tidak aktif')
                                ->orWhereNull('status_siswa');
                        }),
                        blank: fn(Builder $query) => $query,
                    ),
                SelectFilter::make('angkatan')
                    ->label('Angkatan')
                    ->default(
                        fn() => \App\Models\TahunAkademik::query()
                            ->select('nama')
                            ->get()
                            ->map(fn($t) => explode('/', explode(' ', $t->nama)[0])[0])
                            ->unique()
                            ->sortDesc()
                            ->first() ?? 'semua'
                    )
                    ->options(
                        fn() => ['semua' => 'Semua Angkatan', 'belum_ada' => 'Belum Ada Angkatan'] +
                            \App\Models\TahunAkademik::query()
                            ->select('nama')
                            ->get()
                            ->map(fn($t) => explode('/', explode(' ', $t->nama)[0])[0])
                            ->unique()
                            ->sortDesc()
                            ->mapWithKeys(fn($y) => [$y => $y])
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value']) || $data['value'] === 'semua') return $query;

                        if ($data['value'] === 'belum_ada') {
                            // Mahasiswa yang tidak punya riwayat apapun DAN tidak punya data pendaftar
                            return $query
                                ->doesntHave('riwayatPendidikanTerbaru')
                                ->doesntHave('pendaftar');
                        }

                        return $query->where(function ($q) use ($data) {
                            // Prioritas 1: Ada di riwayat pendidikan aktif dengan tahun ini
                            $q->whereHas('riwayatPendidikanAktif', function ($q2) use ($data) {
                                $q2->whereHas('tahunAkademik', function ($q3) use ($data) {
                                    $q3->where('nama', 'like', $data['value'] . '%');
                                });
                            })
                                // Prioritas 2: Jika tidak punya yang aktif, cek di riwayat pendidikan terbaru
                                ->orWhere(function ($q2) use ($data) {
                                    $q2->doesntHave('riwayatPendidikanAktif')
                                        ->whereHas('riwayatPendidikanTerbaru', function ($q3) use ($data) {
                                            $q3->whereHas('tahunAkademik', function ($q4) use ($data) {
                                                $q4->where('nama', 'like', $data['value'] . '%');
                                            });
                                        });
                                })
                                // Prioritas 3: Jika tidak punya riwayat pendidikan sama sekali, cek pendaftar
                                ->orWhere(function ($q2) use ($data) {
                                    $q2->doesntHave('riwayatPendidikanAktif')
                                        ->doesntHave('riwayatPendidikanTerbaru')
                                        ->whereHas('pendaftar', function ($q3) use ($data) {
                                            $q3->whereYear('Tgl_Daftar', $data['value']);
                                        });
                                });
                        });
                    }),
                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(fn() => \App\Models\Jurusan::pluck('nama', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('riwayatPendidikanAktif', function ($q) use ($data) {
                            $q->where('id_jurusan', $data['value']);
                        });
                    }),
                SelectFilter::make('ro_program_kelas')
                    ->label('Program Kelas')
                    ->options(fn() => \App\Models\RefOption\ProgramKelas::pluck('nilai', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('riwayatPendidikanAktif', function ($q) use ($data) {
                            $q->where('ro_program_kelas', $data['value']);
                        });
                    }),
                SelectFilter::make('status_siswa')
                    ->label('Status Siswa')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak aktif' => 'Tidak Aktif',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    Action::make('aktifkan')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Mahasiswa?')
                        ->modalDescription('Riwayat Pendidikan, KRS perdana, dan hak akses murid akan otomatis dibuat.')
                        ->modalSubmitActionLabel('Ya, Aktifkan')
                        ->visible(fn($record) => $record->status_siswa !== 'aktif')
                        ->hidden(function () {
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user && $user->isMurid();
                        })
                        ->action(function ($record) {
                            $success = app(\App\Services\StudentActivationService::class)->activateStudent($record);
                            if ($success) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Aktivasi Berhasil')
                                    ->body('Mahasiswa berhasil diaktifkan.')
                                    ->success()->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Aktivasi Gagal')
                                    ->body('Periksa data jurusan dan program sekolah di data pendaftar.')
                                    ->danger()->send();
                            }
                        }),
                    Action::make('nonaktifkan')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Mahasiswa?')
                        ->modalDescription('Status riwayat pendidikan aktif akan diubah dan hak akses murid akan dicabut.')
                        ->modalSubmitActionLabel('Ya, Nonaktifkan')
                        ->form([
                            \Filament\Forms\Components\Select::make('alasan')
                                ->label('Alasan Penonaktifan')
                                ->options([
                                    'Tidak Aktif' => 'Tidak Aktif',
                                    'Cuti'        => 'Cuti',
                                    'Keluar'      => 'Keluar / Drop Out',
                                    'Lulus'       => 'Lulus',
                                ])
                                ->default('Tidak Aktif')
                                ->required(),
                        ])
                        ->visible(fn($record) => $record->status_siswa === 'aktif')
                        ->hidden(function () {
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user && $user->isMurid();
                        })
                        ->action(function ($record, array $data) {
                            app(\App\Services\StudentActivationService::class)->deactivateStudent($record, $data['alasan']);
                            \Filament\Notifications\Notification::make()
                                ->title('Mahasiswa Dinonaktifkan')
                                ->body('Status mahasiswa telah diubah: ' . $data['alasan'])
                                ->warning()->send();
                        }),
                    Action::make('cetak_ktm')
                        ->label('KTM')
                        ->icon('heroicon-o-identification')
                        ->color('warning')
                        ->url(fn($record) => route('cetak.ktm', $record->id))
                        ->openUrlInNewTab()
                        ->visible(fn($record) => $record->status_siswa === 'aktif' && $record->riwayatPendidikanAktif !== null),
                    Action::make('cetak_transkrip')
                        ->label('Transkrip')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->form([
                            \Filament\Forms\Components\Select::make('id_tahun_akademik')
                                ->label('Tahun Akademik')
                                ->options(
                                    fn() => \App\Models\TahunAkademik::query()
                                        ->orderBy('nama', 'desc')
                                        ->get()
                                        ->pluck('nama', 'id')
                                        ->toArray()
                                )
                                ->required()
                        ])
                        ->modalHeading('Pilih Tahun Akademik')
                        ->modalSubmitActionLabel('Cetak Transkrip')
                        ->visible(fn($record) => $record->riwayatPendidikanAktif !== null)
                        ->action(function ($record, array $data, \Livewire\Component $livewire) {
                            $url = route('cetak.transkrip', ['id' => $record->riwayatPendidikanAktif->id, 'tahun' => $data['id_tahun_akademik']]);
                            $livewire->js("window.open('{$url}', '_blank');");
                        }),
                    Action::make('view_grades')
                        ->label('Nilai')
                        ->icon('heroicon-o-academic-cap')
                        ->color('info')
                        ->url(fn($record) => \App\Filament\Resources\SiswaDataLJKS\SiswaDataLJKResource::getUrl('index', [
                            'tableFilters' => [
                                'id_akademik_krs' => [
                                    'value' => $record->akademikKrs->first()?->id,
                                ],
                            ],
                        ])),
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                                ->fromModel(),
                        ]),
                    \Filament\Actions\BulkAction::make('export_pddikti')
                        ->label('Export PDDIKTI')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\MahasiswaPddiktiExport($records),
                                'mahasiswa_pddikti_' . date('Ymd_His') . '.xlsx'
                            );
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Data Mahasiswa (PDDIKTI)')
                        ->modalDescription('Apakah Anda yakin ingin mengekspor data mahasiswa yang dipilih ke dalam format Excel PDDIKTI?'),
                    \Filament\Actions\BulkAction::make('hapus_permanen')
                        ->label('Hapus Permanen')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Mahasiswa Terpilih?')
                        ->modalDescription('Semua data terkait (Riwayat Pendidikan, KRS, Nilai, Akun Login) akan ikut terhapus secara permanen dan TIDAK BISA dikembalikan!')
                        ->modalSubmitActionLabel('Ya, Hapus Semua')
                        ->hidden(function () {
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user && $user->isMurid();
                        })
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $deleted = 0;
                            foreach ($records as $record) {
                                try {
                                    // 1. Hapus Nilai/LJK via KRS
                                    foreach ($record->riwayatPendidikan as $riwayat) {
                                        foreach ($riwayat->akademikKrs as $krs) {
                                            $krs->siswaDataLjk()->delete();
                                            $krs->delete();
                                        }
                                        // Hapus data terkait riwayat lainnya
                                        \App\Models\PengajuanSurat::where('id_riwayat_pendidikan', $riwayat->id)->delete();
                                        \App\Models\TaSkripsi::where('id_riwayat_pendidikan', $riwayat->id)->delete();
                                        \App\Models\TaSeminarProposal::where('id_riwayat_pendidikan', $riwayat->id)->delete();
                                        \App\Models\TaPengajuanJudul::where('id_riwayat_pendidikan', $riwayat->id)->delete();
                                        \App\Models\WisudaMahasiswa::where('id_riwayat_pendidikan', $riwayat->id)->delete();
                                        $riwayat->delete();
                                    }

                                    // Hapus PKL yang terkait langsung dengan siswa
                                    \App\Models\PklPendaftaran::where('id_siswa_data', $record->id)->delete();

                                    // 2. Hapus data pendaftar & seleksi
                                    if ($record->pendaftar) {
                                        \App\Models\SiswaSeleksiPendaftar::where('id_siswa_data_pendaftar', $record->pendaftar->id)->delete();
                                        $record->pendaftar->delete();
                                    }

                                    // 3. Hapus data orang tua
                                    if ($record->orangTua) $record->orangTua->delete();

                                    // 4. Simpan user_id sebelum record dihapus
                                    $userId = $record->user_id;

                                    // 5. Hapus siswa_data
                                    $record->delete();

                                    // 6. Hapus akun user
                                    if ($userId) {
                                        \App\Models\User::find($userId)?->delete();
                                    }

                                    $deleted++;
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error("Gagal hapus siswa ID {$record->id}: " . $e->getMessage());
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Penghapusan Selesai')
                                ->body("{$deleted} mahasiswa beserta seluruh datanya berhasil dihapus.")
                                ->success()
                                ->send();
                        }),
                    \Filament\Actions\BulkAction::make('aktifkan_serentak')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Mahasiswa Terpilih?')
                        ->modalDescription('Riwayat Pendidikan, KRS perdana, dan hak akses murid akan otomatis dibuat untuk setiap mahasiswa yang dipilih.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $service = app(\App\Services\StudentActivationService::class);
                            $successCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                $result = $service->activateStudent($record);
                                $result ? $successCount++ : $skippedCount++;
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Aktivasi Massal Selesai')
                                ->body("{$successCount} mahasiswa diaktifkan. {$skippedCount} dilewati (data tidak lengkap).")
                                ->success()
                                ->send();
                        })
                        ->hidden(function () {
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user && $user->isMurid();
                        }),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                // Join pendaftar and users to sort by the same fallback name logic
                $query->leftJoin('siswa_data_pendaftar as sdp_sort', 'sdp_sort.id_siswa_data', '=', 'siswa_data.id')
                    ->leftJoin('users as usr_sort', 'usr_sort.id', '=', 'siswa_data.user_id')
                    ->orderByRaw('COALESCE(siswa_data.nama_lengkap, sdp_sort.Nama_Lengkap, usr_sort.name) ASC')
                    ->select('siswa_data.*');
            })
            ->headerActions([
                \Filament\Actions\Action::make('import')
                    ->label('Import Mahasiswa')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->storeFiles(false)
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $file = is_array($data['file']) ? $data['file'][0] : $data['file'];
                        $filePath = $file->getRealPath();
                        $import = new \App\Imports\SiswaDataImport();
                        \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                        \Filament\Notifications\Notification::make()
                            ->title('Import Selesai')
                            ->body($import->successCount . ' baris berhasil diimpor.')
                            ->success()
                            ->send();
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn () => 'Data_Mahasiswa_' . date('Y-m-d_H-i-s')),
                    ]),
            ])
            ->paginationPageOptions([25, 50, 100, 'all']);
    }
}
