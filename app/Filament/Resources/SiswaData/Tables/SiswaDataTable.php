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
use Filament\Tables\Columns\SelectColumn;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\SiswaData\SiswaDataResource;

class SiswaDataTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_profil')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=' . urlencode('Siswa'))),
                TextColumn::make('riwayatPendidikanAktif.angkatan')
                    ->label('Angkatan')
                    ->toggleable(),
                TextColumn::make('nama')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('riwayatPendidikanAktif.nomor_induk')
                    ->label('Nomor Induk')
                    ->searchable()
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
                    ->label('Status Pendidikan')
                    ->searchable()
                    ->toggleable(),
                SelectColumn::make('status_siswa')
                    ->label('Status Siswa')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak aktif' => 'Tidak Aktif',
                    ])
                    ->sortable()
                    ->searchable()
                    ->updateStateUsing(function ($record, $state) {
                        if ($state === 'aktif') {
                            $pendaftar = $record->pendaftar;

                            if (!$pendaftar) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Data Pendaftar Tidak Ditemukan')
                                    ->body('Siswa ini tidak memiliki data pendaftaran.')
                                    ->warning()
                                    ->send();
                                return $record->status_siswa;
                            }

                            if (
                                !$pendaftar->id_jurusan ||
                                !$pendaftar->ro_program_sekolah
                            ) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Data Belum Lengkap')
                                    ->body('Jurusan atau Program Sekolah belum terisi di data pendaftar.')
                                    ->danger()
                                    ->send();
                                return $record->status_siswa;
                            }

                            // Cari status siswa "Aktif" di RefOption
                            $statusSiswaAktif = \App\Models\RefOption\StatusSiswa::where('nilai', 'Aktif')->first();
                            $idStatusSiswa = $statusSiswaAktif ? $statusSiswaAktif->id : null;

                            // Cek apakah sudah ada riwayat pendidikan yang sama
                            $exists = \App\Models\RiwayatPendidikan::where('id_siswa_data', $record->id)
                                ->where('id_jurusan', $pendaftar->id_jurusan)
                                ->where('ro_program_sekolah', $pendaftar->ro_program_sekolah)
                                ->exists();

                            if (!$exists) {
                                $riwayat = \App\Models\RiwayatPendidikan::create([
                                    'id_siswa_data' => $record->id,
                                    'id_jurusan' => $pendaftar->id_jurusan,
                                    'ro_program_sekolah' => $pendaftar->ro_program_sekolah,
                                    'id_tahun_akademik' => $pendaftar->id_tahun_akademik ?? \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id,
                                    'tanggal_mulai' => now(),
                                    'status' => 'Aktif',
                                    'ro_status_siswa' => $idStatusSiswa, // Set status siswa di riwayat pendidikan
                                ]);

                                // Buat Akademik KRS Pertama otomatis
                                $tahunAkademikAktif = \App\Models\TahunAkademik::where('status', 'aktif')->first();
                                \App\Models\AkademikKrs::create([
                                    'id_riwayat_pendidikan' => $riwayat->id,
                                    'jumlah_sks' => 24,
                                    'tgl_krs' => now(),
                                    'kode_tahun' => $tahunAkademikAktif?->nama ?? (date('Y') . '/' . (date('Y') + 1)),
                                    'status_bayar' => 'N',
                                    'syarat_uts' => 'N',
                                    'syarat_uas' => 'N',
                                    'syarat_krs' => 'N',
                                    'status_aktif' => 'Y',
                                ]);

                                if ($record->user_id) {
                                    $userTarget = \App\Models\User::find($record->user_id);
                                    if ($userTarget) {
                                        $userTarget->assignRole('murid');
                                        $userTarget->removeRole('pendaftar');
                                    }
                                }

                                \Filament\Notifications\Notification::make()
                                    ->title('Aktivasi Berhasil')
                                    ->body('Riwayat Pendidikan, Akademik KRS dan Role Murid berhasil ditambahkan.')
                                    ->success()
                                    ->send();
                            } else {
                                // Jika sudah ada, update statusnya jika perlu (opsional based on requirement, here we keep it simple)
                                \Filament\Notifications\Notification::make()
                                    ->title('Info')
                                    ->body('Riwayat pendidikan sudah ada.')
                                    ->info()
                                    ->send();
                            }
                        }

                        $record->update(['status_siswa' => $state]);
                        return $state;
                    })
                    ->disabled(function () {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user && $user->isMurid();
                    }),
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
                SelectFilter::make('status_siswa')
                    ->label('Status Siswa')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak aktif' => 'Tidak Aktif',
                    ]),
            ])
            ->recordActions([
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
                    ->url(fn($record) => route('cetak.transkrip', $record->id))
                    ->openUrlInNewTab(),
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
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('aktifkan_serentak')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $successCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                // Update status on record
                                $record->status_siswa = 'aktif';
                                $record->save();

                                $pendaftar = $record->pendaftar;

                                if (!$pendaftar || !$pendaftar->id_jurusan || !$pendaftar->ro_program_sekolah) {
                                    $skippedCount++;
                                    continue;
                                }

                                $exists = \App\Models\RiwayatPendidikan::where('id_siswa_data', $record->id)
                                    ->where('id_jurusan', $pendaftar->id_jurusan)
                                    ->where('ro_program_sekolah', $pendaftar->ro_program_sekolah)
                                    ->exists();

                                if (!$exists) {
                                    $riwayat = \App\Models\RiwayatPendidikan::create([
                                        'id_siswa_data' => $record->id,
                                        'id_jurusan' => $pendaftar->id_jurusan,
                                        'ro_program_sekolah' => $pendaftar->ro_program_sekolah,
                                        'id_tahun_akademik' => $pendaftar->id_tahun_akademik ?? \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id,
                                        'tanggal_mulai' => now(),
                                        'status' => 'Aktif',
                                    ]);

                                    // Buat Akademik KRS Pertama otomatis
                                    $tahunAkademikAktif = \App\Models\TahunAkademik::where('status', 'aktif')->first();
                                    \App\Models\AkademikKrs::create([
                                        'id_riwayat_pendidikan' => $riwayat->id,
                                        'jumlah_sks' => 24,
                                        'tgl_krs' => now(),
                                        'kode_tahun' => $tahunAkademikAktif?->nama ?? date('Y') . '/' . (date('Y') + 1),
                                        'status_bayar' => 'N',
                                        'syarat_uts' => 'N',
                                        'syarat_uas' => 'N',
                                        'syarat_krs' => 'N',
                                        'status_aktif' => 'Y',
                                        'created_at' => now(),
                                    ]);

                                    if ($record->user_id) {
                                        $userTarget = \App\Models\User::find($record->user_id);
                                        if ($userTarget) {
                                            $userTarget->assignRole('murid');
                                            $userTarget->removeRole('pendaftar');
                                        }
                                    }

                                    $successCount++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Aktivasi Massal Selesai')
                                ->body("{$successCount} siswa diaktifkan (Riwayat Pendidikan, KRS, dan Role Murid ditambahkan). {$skippedCount} dilewati (data tidak lengkap).")
                                ->success()
                                ->send();
                        })
                        ->disabled(function () {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user && $user->isMurid();
                        }),
                ]),
            ])
            // ->toolbarActions([])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
                // Action::make('download_arsip')
                //     ->label('Download Arsip')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->color('success')
                //     ->url(fn(): string => SiswaDataResource::getUrl('download-files')),
            ]);
    }
}
