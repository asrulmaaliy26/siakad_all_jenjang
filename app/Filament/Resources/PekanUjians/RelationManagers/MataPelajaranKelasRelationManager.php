<?php

namespace App\Filament\Resources\PekanUjians\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Actions\Action;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;

class MataPelajaranKelasRelationManager extends RelationManager
{
    protected static string $relationship = 'mataPelajaranKelas'; // Sesuaikan dengan nama relasi di model PekanUjian

    protected static ?string $title = 'Mata Pelajaran Kelas';

    protected static ?string $recordTitleAttribute = 'id_mata_pelajaran_kelas';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = \Filament\Facades\Filament::auth()->user();
                return MataPelajaranKelas::query()
                    ->whereHas('kelas', function (Builder $query) {
                        // Filter: Hanya tampilkan mata pelajaran kelas yang berada di tahun akademik yang sama dengan Pekan Ujian
                        $query->where('id_tahun_akademik', $this->getOwnerRecord()->id_tahun_akademik);
                    })
                    ->when(
                        $user && $user->isPengajar(),
                        fn(Builder $query) => $query->whereHas('dosenData', fn(Builder $q) => $q->where('user_id', $user->id))
                    )
                    ->when(
                        $user && $user->isMurid(),
                        fn(Builder $query) => $query->whereHas('siswaDataLjk.akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                    );
            })
            ->recordTitleAttribute('id_mata_pelajaran_kelas')
            ->columns([
                Tables\Columns\TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.semester')
                    ->label('Semester')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.programKelas.nilai')
                    ->label('Nama Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('dosenData.nama')
                    ->label('Guru Pengampu')
                    ->searchable(),
                // Perbaikan: Ambil tahun akademik dari kelas -> tahunAkademik
                // Tables\Columns\TextColumn::make('kelas.tahunAkademik.nama')
                //     ->label('Tahun Akademik')
                //     ->getStateUsing(function ($record) {
                //         return $record->kelas?->tahunAkademik?->nama . ' - ' .
                //             $record->kelas?->tahunAkademik?->periode ?? '-';
                //     })
                //     ->badge()
                //     ->color('success')
                //     ->sortable(query: function ($query, $direction) {
                //         // Custom sorting melalui relasi
                //         return $query->orderBy(
                //             Kelas::select('id_tahun_akademik')
                //                 ->whereColumn('kelas.id', 'mata_pelajaran_kelas.id_kelas')
                //                 ->limit(1),
                //             $direction
                //         );
                //     }),
                Tables\Columns\ToggleColumn::make(
                    str_contains(strtolower($this->getOwnerRecord()->jenis_ujian ?? ''), 'uas')
                        ? 'status_uas'
                        : 'status_uts'
                )
                    ->label('Status Ujian')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(fn() => auth()->user()?->isMurid()),
                Tables\Columns\TextColumn::make('soal_check')
                    ->label('Soal')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $pekanUjian = $this->getOwnerRecord();
                        $jenisUjian = strtolower($pekanUjian->jenis_ujian ?? '');

                        $file = null;
                        $note = null;

                        if (str_contains($jenisUjian, 'uts')) {
                            $file = $record->soal_uts;
                            $note = $record->ctt_soal_uts;
                        } elseif (str_contains($jenisUjian, 'uas')) {
                            $file = $record->soal_uas;
                            $note = $record->ctt_soal_uas;
                        }

                        return \App\Helpers\UjianHelper::hasSubmission($file, $note) ? 'Mulai Ujian' : '-';
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Mulai Ujian' => 'info',
                        '-' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program_kelas_id')
                    ->label('Kelas')
                    ->options(function () {
                        return \App\Models\RefOption\ProgramKelas::where('nama_grup', 'program_kelas')
                            ->pluck('nilai', 'id');
                    })
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['value'], function (Builder $query, $value) {
                            $query->whereHas('kelas', function (Builder $query) use ($value) {
                                $query->where('ro_program_kelas', $value);
                            });
                        });
                    }),
                Tables\Filters\SelectFilter::make('id_dosen_data')
                    ->label('Guru')
                    ->relationship('dosenData', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->relationship('kelas.jurusan', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('semester')
                    ->label('Semester')
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
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['value'], function (Builder $query, $value) {
                            $query->whereHas('kelas', function (Builder $query) use ($value) {
                                $query->where('semester', $value);
                            });
                        });
                    }),
            ])
            ->headerActions([
                // Tidak perlu tambah data karena ini hanya menampilkan
            ])
            ->actions([
                Action::make('mulai_ujian')
                    ->label('Mulai Ujian')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(function ($record) {
                        $pekanUjian  = $this->getOwnerRecord();
                        $jenisUjian  = strtolower($pekanUjian->jenis_ujian ?? '');
                        $isUas       = str_contains($jenisUjian, 'uas');
                        $statusCol   = $isUas ? 'status_uas' : 'status_uts';
                        $syaratCol   = $isUas ? 'syarat_uas' : 'syarat_uts';
                        $type        = $isUas ? 'uas' : 'uts';
                        $cekalCol    = $isUas ? 'cekal_ujian_uas' : 'cekal_ujian_uts';

                        if (! $record->{$statusCol}) return false;

                        $user = \Filament\Facades\Filament::auth()->user();
                        if (!$user || !$user->isMurid()) return false;

                        $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
                        if (! $siswa) return false;

                        $siswaLjk = \App\Models\SiswaDataLJK::withoutGlobalScopes()
                            ->where('id_mata_pelajaran_kelas', $record->id)
                            ->whereHas('akademikKrs', function ($q) use ($siswa) {
                                $q->withoutGlobalScopes()->whereHas('riwayatPendidikan', function ($sq) use ($siswa) {
                                    $sq->withoutGlobalScopes()->where('id_siswa_data', $siswa->id);
                                });
                            })
                            ->first();

                        if ($siswaLjk && $siswaLjk->cekal_kuliah === 'Y') return false;

                        // Jika sudah tercekal ujian, sembunyikan tombol
                        if ($siswaLjk && $siswaLjk->$cekalCol === 'Y') return false;

                        $krs = \App\Models\AkademikKrs::whereHas(
                            'siswaDataLjk', fn($q) => $q->where('id_mata_pelajaran_kelas', $record->id)
                        )->whereHas(
                            'riwayatPendidikan', fn($q) => $q->where('id_siswa_data', $siswa->id)
                        )->first();

                        if (! $krs) return false;

                        if ($pekanUjian->status_bayar) {
                            if (($krs->{$syaratCol} ?? 'N') !== 'Y') return false;
                        }

                        return true;
                    })
                    ->url(function ($record) {
                        $pekanUjian = $this->getOwnerRecord();
                        $jenisUjian = strtolower($pekanUjian->jenis_ujian ?? '');
                        $type = str_contains($jenisUjian, 'uas') ? 'uas' : 'uts';
                        return route('ujian.fullscreen', [$record->id, $type]);
                    })
                    ->openUrlInNewTab(false),

                Action::make('cekal_badge')
                    ->label('Diblokir (3 Pelanggaran)')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->disabled()
                    ->visible(function ($record) {
                        $user = \Filament\Facades\Filament::auth()->user();
                        if (!$user || !$user->isMurid()) return false;
                        $pekanUjian = $this->getOwnerRecord();
                        $jenisUjian = strtolower($pekanUjian->jenis_ujian ?? '');
                        $isUas   = str_contains($jenisUjian, 'uas');
                        $cekalCol = $isUas ? 'cekal_ujian_uas' : 'cekal_ujian_uts';
                        $statusCol = $isUas ? 'status_uas' : 'status_uts';
                        if (!$record->{$statusCol}) return false;
                        $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
                        if (!$siswa) return false;
                        $ljk = \App\Models\SiswaDataLJK::withoutGlobalScopes()
                            ->where('id_mata_pelajaran_kelas', $record->id)
                            ->whereHas('akademikKrs', function ($q) use ($siswa) {
                                $q->withoutGlobalScopes()->whereHas('riwayatPendidikan', function ($sq) use ($siswa) {
                                    $sq->withoutGlobalScopes()->where('id_siswa_data', $siswa->id);
                                });
                            })->first();
                        return $ljk && $ljk->$cekalCol === 'Y';
                    }),

                Action::make('reset_cekal_ujian')
                    ->label('Reset Akses Ujian')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Akses Ujian Mahasiswa')
                    ->modalDescription('Apakah Anda yakin ingin mereset status cekal ujian mahasiswa ini? Log pelanggaran akan tetap tersimpan.')
                    ->modalSubmitActionLabel('Ya, Reset')
                    ->visible(fn() => ! auth()->user()?->isMurid())
                    ->action(function ($record) {
                        $pekanUjian = $this->getOwnerRecord();
                        $jenisUjian = strtolower($pekanUjian->jenis_ujian ?? '');
                        $isUas = str_contains($jenisUjian, 'uas');
                        $cekalCol = $isUas ? 'cekal_ujian_uas' : 'cekal_ujian_uts';
                        $jmlCol   = $isUas ? 'jml_pelanggaran_uas' : 'jml_pelanggaran_uts';

                        \App\Models\SiswaDataLJK::withoutGlobalScopes()
                            ->where('id_mata_pelajaran_kelas', $record->id)
                            ->update([$cekalCol => 'N', $jmlCol => 0]);

                        \Filament\Notifications\Notification::make()
                            ->title('Akses Ujian Berhasil Direset')
                            ->body('Semua mahasiswa di mata pelajaran ini dapat kembali mengakses ujian.')
                            ->success()
                            ->send();
                    }),

                Action::make('lihat_soal_admin')
                    ->label('Lihat Soal')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn() => ! auth()->user()?->isMurid())
                    ->modalHeading(fn($record) => 'Detail Soal - ' . $record->mataPelajaranKurikulum?->mataPelajaranMaster?->nama)
                    ->modalContent(function ($record, $livewire) {
                        $pekanUjian = $livewire->getOwnerRecord();
                        $jenisUjian = strtolower($pekanUjian->jenis_ujian ?? '');
                        $type = str_contains($jenisUjian, 'uas') ? 'uas' : 'uts';
                        return view('filament.resources.pekan-ujians.actions.view-soal', [
                            'record' => $record,
                            'type'   => $type,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalWidth('7xl')
                    ->modalCancelAction(fn() => \Filament\Actions\Action::make('tutup')->label('Tutup')->close()),
            ])
            ->bulkActions([
                BulkAction::make('update_status')
                    ->label('Set Status Ujian')
                    ->icon('heroicon-o-check-circle')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Y' => 'Aktif',
                                'N' => 'Tidak Aktif',
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data, $livewire) {
                        $pekanUjian = $livewire->getOwnerRecord();
                        // Asumsi jenis_ujian mengandung string 'UTS' atau 'UAS' (case-insensitive)
                        $jenisUjian = strtolower($pekanUjian->jenis_ujian ?? '');

                        $column = null;
                        if (str_contains($jenisUjian, 'uts')) {
                            $column = 'status_uts';
                        } elseif (str_contains($jenisUjian, 'uas')) {
                            $column = 'status_uas';
                        }

                        if ($column) {
                            $records->each(function ($record) use ($column, $data) {
                                $record->update([
                                    $column => $data['status'],
                                ]);
                            });

                            Notification::make()
                                ->title('Berhasil Diperbarui')
                                ->body('Status ujian untuk ' . $records->count() . ' mata pelajaran telah diubah menjadi ' . ($data['status'] === 'Y' ? 'Aktif' : 'Tidak Aktif') . '.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal Update Status')
                                ->body('Jenis ujian pada Pekan Ujian tidak terdeteksi sebagai UTS atau UAS. (Jenis: ' . ($pekanUjian->jenis_ujian ?? 'Kosong') . ')')
                                ->warning()
                                ->send();
                        }
                    })
                    ->deselectRecordsAfterCompletion()
                    ->disabled(fn() => auth()->user() && auth()->user()->isMurid()),
            ]);
    }

    // Optional: Method untuk mengecek apakah user bisa mengakses relation manager
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // Hanya tampilkan jika owner record memiliki id_tahun_akademik
        return $ownerRecord->id_tahun_akademik !== null;
    }
}
