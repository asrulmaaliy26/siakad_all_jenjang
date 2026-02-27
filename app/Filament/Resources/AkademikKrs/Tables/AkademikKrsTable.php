<?php

namespace App\Filament\Resources\AkademikKrs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\ActionGroup;
use Carbon\Carbon;

class AkademikKrsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // Relasi / Foreign Key
                TextColumn::make('riwayatPendidikan.siswaData.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('riwayatPendidikan.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('NIM berhasil disalin')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-clipboard')
                    ->iconPosition('after')
                    ->toggleable(),

                TextColumn::make('riwayatPendidikan.waliDosen.nama')
                    ->label('Wali Dosen')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user && !$user->isMurid();
                    }),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        return $record->riwayatPendidikan?->getSemester($record->tgl_krs ?? $record->created_at);
                    })
                    ->formatStateUsing(fn($state) => "Semester {$state}")
                    ->icon('heroicon-o-academic-cap')
                    ->iconPosition('before')
                    ->toggleable(),

                TextColumn::make('jumlah_sks')
                    ->label('SKS')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state >= 20 ? 'success' : ($state >= 15 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn($state) => "{$state} SKS")
                    ->icon('heroicon-o-calculator')
                    ->iconPosition('before')
                    ->toggleable(),

                TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->formatStateUsing(fn($record) => $record->tahunAkademik ? "{$record->tahunAkademik->nama} - {$record->tahunAkademik->periode}" : $record->kode_tahun)
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(),

                // Status Bayar dengan SelectColumn yang mendukung dark mode
                SelectColumn::make('status_bayar')
                    ->label('Status Bayar')
                    ->options([
                        'Y' => 'Lunas',
                        'N' => 'Belum Lunas',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->disabled(function () {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return $user && ($user->isMurid() || $user->isPengajar());
                    })
                    ->extraAttributes(function ($state) {
                        $classes = [
                            'Y' => 'status-badge status-success',
                            'N' => 'status-badge status-danger',
                        ];
                        return ['class' => $classes[$state] ?? 'status-badge status-default'];
                    }),

                // Syarat UTS dengan SelectColumn
                SelectColumn::make('syarat_uts')
                    ->label('Syarat UTS')
                    ->options([
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                    ])
                    ->selectablePlaceholder(false)
                    ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar())
                    ->extraAttributes(function ($state) {
                        $classes = [
                            'Y' => 'status-badge status-success',
                            'N' => 'status-badge status-warning',
                        ];
                        return ['class' => $classes[$state] ?? 'status-badge status-default'];
                    }),

                // Syarat UAS dengan SelectColumn
                SelectColumn::make('syarat_uas')
                    ->label('Syarat UAS')
                    ->options([
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                    ])
                    ->selectablePlaceholder(false)
                    ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar())
                    ->extraAttributes(function ($state) {
                        $classes = [
                            'Y' => 'status-badge status-success',
                            'N' => 'status-badge status-warning',
                        ];
                        return ['class' => $classes[$state] ?? 'status-badge status-default'];
                    }),

                // Syarat KRS dengan SelectColumn
                SelectColumn::make('syarat_krs')
                    ->label('Syarat KRS')
                    ->options([
                        'Y' => 'Disetujui',
                        'N' => 'Menunggu Persetujuan',
                    ])
                    ->selectablePlaceholder(false)
                    ->disabled(function () {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return $user && $user->isMurid();
                    })
                    ->extraAttributes(function ($state) {
                        $classes = [
                            'Y' => 'status-badge status-success',
                            'N' => 'status-badge status-warning',
                        ];
                        return ['class' => $classes[$state] ?? 'status-badge status-default'];
                    }),

                // Status Aktif dengan SelectColumn
                SelectColumn::make('status_aktif')
                    ->label('Status Aktif')
                    ->options([
                        'Y' => 'Aktif',
                        'N' => 'Tidak Aktif',
                    ])
                    ->selectablePlaceholder(false)
                    ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar())
                    ->extraAttributes(function ($state) {
                        $classes = [
                            'Y' => 'status-badge status-success',
                            'N' => 'status-badge status-danger',
                        ];
                        return ['class' => $classes[$state] ?? 'status-badge status-default'];
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state === 'N') {
                            // Cek syarat bayar
                            if ($record->status_bayar !== 'Y') {
                                // Revert status_aktif jika belum bayar
                                $record->update(['status_aktif' => 'Y']);

                                Notification::make()
                                    ->title('Gagal Menonaktifkan')
                                    ->body('KRS tidak dapat dinonaktifkan karena status pembayaran belum disetujui atau belum lunas.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Cek status mahasiswa (Aktif/Tidak)
                            $statusMhs = $record->riwayatPendidikan?->statusSiswa?->nilai ?? 'Tidak Diketahui';
                            if (strtolower($statusMhs) !== 'aktif') {
                                // Revert status_aktif jika mahasiswa tidak aktif
                                $record->update(['status_aktif' => 'Y']);

                                Notification::make()
                                    ->title('Gagal Menonaktifkan')
                                    ->body("KRS tidak dapat dinonaktifkan karena status Mahasiswa saat ini adalah: {$statusMhs}. Mahasiswa harus berstatus 'Aktif'.")
                                    ->warning()
                                    ->send();
                                return;
                            }

                            try {
                                $record->deactivateAndCreateNew();
                                Notification::make()
                                    ->title('Berhasil')
                                    ->body('KRS telah dinonaktifkan dan KRS baru untuk semester berikutnya telah dibuat otomatis.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                // Revert status_aktif jika gagal proses deaktifasi
                                $record->update(['status_aktif' => 'Y']);

                                Notification::make()
                                    ->title('Gagal')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    }),

                TextColumn::make('kwitansi_krs')
                    ->label('Kwitansi')
                    ->getStateUsing(fn($record) => count($record->kwitansi_krs ?? []) > 0 ? count($record->kwitansi_krs) . ' File' : '-')
                    ->badge()
                    ->color(fn($state) => $state !== '-' ? 'success' : 'gray')
                    ->icon('heroicon-o-document-check')
                    ->toggleable()
                    ->action(
                        Action::make('view_kwitansi')
                            ->modalHeading('Lihat Kwitansi')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->fillForm(fn($record) => [
                                'kwitansi_krs' => $record->kwitansi_krs,
                            ])
                            ->form([
                                FileUpload::make('kwitansi_krs')
                                    ->label('Berkas Kwitansi')
                                    ->multiple()
                                    ->disk('public')
                                    ->disabled()
                                    ->openable()
                                    ->downloadable()
                                    ->dehydrated(false)
                            ])
                    ),

                TextColumn::make('berkas_lain')
                    ->label('Berkas')
                    ->getStateUsing(fn($record) => count($record->berkas_lain ?? []) > 0 ? count($record->berkas_lain) . ' File' : '-')
                    ->badge()
                    ->color(fn($state) => $state !== '-' ? 'success' : 'gray')
                    ->icon('heroicon-o-paper-clip')
                    ->toggleable()
                    ->action(
                        Action::make('view_berkas_lain')
                            ->modalHeading('Lihat Berkas Pendukung')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->fillForm(fn($record) => [
                                'berkas_lain' => $record->berkas_lain,
                            ])
                            ->form([
                                FileUpload::make('berkas_lain')
                                    ->label('Berkas Pendukung')
                                    ->multiple()
                                    ->disk('public')
                                    ->disabled()
                                    ->openable()
                                    ->downloadable()
                                    ->dehydrated(false)
                            ])
                    ),

                // Created At
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray')
                    ->size('sm'),

                // Updated At
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray')
                    ->size('sm'),

            ])
            ->filters([
                SelectFilter::make('semester')
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
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return;

                        $query->whereHas('riwayatPendidikan', function ($q) use ($data) {
                            $targetSemester = (int) $data['value'];

                            // Logika filter ini harus sinkron dengan RiwayatPendidikan::getSemester()
                            // getSemester() menggunakan floor(diffInMonths / 6) + 1
                            // Maka difilter berdasarkan rentang bulan (6 * (semester - 1) <= diff <= 6 * semester - 1)

                            $startMonth = ($targetSemester - 1) * 6;
                            $endMonth = ($targetSemester * 6) - 1;

                            $q->whereRaw("TIMESTAMPDIFF(MONTH, tanggal_mulai, COALESCE(akademik_krs.tgl_krs, akademik_krs.created_at)) BETWEEN ? AND ?", [$startMonth, $endMonth]);
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('kode_tahun')
                    ->label('Tahun Akademik')
                    ->options(fn() => \App\Models\TahunAkademik::all()->mapWithKeys(fn($item) => [$item->nama => "{$item->nama} - {$item->periode}"])->toArray())
                    ->default(\App\Models\TahunAkademik::where('status', 'Aktif')->first()?->nama)
                    ->searchable()
                    ->native(false),

                SelectFilter::make('status_bayar')
                    ->label('Status Bayar')
                    ->options([
                        'Y' => 'Lunas',
                        'N' => 'Belum Lunas',
                    ])
                    ->native(false),

                SelectFilter::make('status_aktif')
                    ->label('Status Aktif')
                    ->options([
                        'Y' => 'Aktif',
                        'N' => 'Tidak Aktif',
                    ])
                    ->native(false),
            ])
            ->headerActions([])
            ->actions([
                ActionGroup::make([
                    Action::make('cetak_krs')
                        ->label('Cetak KRS')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn($record) => route('cetak.krs', $record->id))
                        ->openUrlInNewTab(),

                    Action::make('cetak_khs')
                        ->label('Cetak KHS')
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('info')
                        ->url(fn($record) => route('cetak.khs', $record->id))
                        ->openUrlInNewTab(),

                    ViewAction::make()
                        ->label('Lihat')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detail KRS')
                        ->modalWidth('7xl'),

                    Action::make('view_subjects')
                        ->label('Mata Pelajaran')
                        ->icon('heroicon-o-book-open')
                        ->color('warning')
                        ->modalHeading('Daftar Mata Pelajaran')
                        ->modalContent(fn($record) => view('filament.resources.akademik-krs.actions.view-subjects', ['record' => $record]))
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->closeModalByClickingAway(false)
                        ->modalWidth('7xl')
                        ->visible(fn() => ! auth()->user()?->isMurid()),

                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->color('primary')
                        ->modalHeading('Edit KRS')
                        ->modalWidth('2xl'),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus KRS')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->visible(fn() => ! auth()->user()?->isMurid()),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Aksi')
            ])
            ->bulkActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                BulkAction::make('update_status')
                    ->label('Update Status Terpilih')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Select::make('status_aktif')
                            ->label('Status Aktif')
                            ->options([
                                'Y' => 'Aktif',
                                'N' => 'Tidak Aktif',
                            ])
                            ->placeholder('Pilih Status Aktif...'),
                        Select::make('status_bayar')
                            ->label('Status Bayar')
                            ->options([
                                'Y' => 'Lunas',
                                'N' => 'Belum Lunas',
                            ])
                            ->placeholder('Pilih Status Bayar...'),
                        Select::make('syarat_uts')
                            ->label('Syarat UTS')
                            ->options([
                                'Y' => 'Terpenuhi',
                                'N' => 'Belum Terpenuhi',
                            ])
                            ->placeholder('Pilih Syarat UTS...'),
                        Select::make('syarat_uas')
                            ->label('Syarat UAS')
                            ->options([
                                'Y' => 'Terpenuhi',
                                'N' => 'Belum Terpenuhi',
                            ])
                            ->placeholder('Pilih Syarat UAS...'),
                        Select::make('syarat_krs')
                            ->label('Syarat KRS')
                            ->options([
                                'Y' => 'Disetujui',
                                'N' => 'Menunggu Persetujuan',
                            ])
                            ->selectablePlaceholder(false)
                            ->disabled(fn() => auth()->user()?->isMurid())
                            ->extraAttributes(function ($state) {
                                $classes = [
                                    'Y' => 'status-badge status-success',
                                    'N' => 'status-badge status-warning',
                                ];
                                return ['class' => $classes[$state] ?? 'status-badge status-default'];
                            }),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $updateData = array_filter($data, fn($value) => $value !== null);

                        if (empty($updateData)) {
                            Notification::make()
                                ->title('Peringatan')
                                ->body('Tidak ada status yang dipilih untuk diperbarui.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Jika status_aktif diubah ke 'N', gunakan logika deaktifasi
                        // if (isset($updateData['status_aktif']) && $updateData['status_aktif'] === 'N') {
                        //     $successCount = 0;
                        //     $errorMessages = [];

                        //     foreach ($records as $record) {
                        //         try {
                        //             // Update field lain dulu jika ada
                        //             $otherUpdates = array_diff_key($updateData, ['status_aktif' => '']);
                        //             if (!empty($otherUpdates)) {
                        //                 $record->update($otherUpdates);
                        //             }

                        //             // Jalankan deaktifasi dan pembuatan KRS baru
                        //             $record->deactivateAndCreateNew();
                        //             $successCount++;
                        //         } catch (\Exception $e) {
                        //             $mhsName = $record->riwayatPendidikan->siswaData->nama ?? 'Siswa';
                        //             $errorMessages[] = "{$mhsName}: " . $e->getMessage();
                        //         }
                        //     }

                        //     if ($successCount > 0) {
                        //         Notification::make()
                        //             ->title('Proses Selesai')
                        //             ->body("{$successCount} data KRS berhasil dinonaktifkan dan diperbarui.")
                        //             ->success()
                        //             ->send();
                        //     }

                        //     if (!empty($errorMessages)) {
                        //         Notification::make()
                        //             ->title('Beberapa Gagal')
                        //             ->body(implode("\n", $errorMessages))
                        //             ->danger()
                        //             ->persistent()
                        //             ->send();
                        //     }
                        // } else {
                        // Update normal untuk status lainnya atau status_aktif = 'Y'
                        $records->each(fn($record) => $record->update($updateData));

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Status ' . count($records) . ' data KRS berhasil diperbarui.')
                            ->success()
                            ->send();
                        // }
                    })
                    ->deselectRecordsAfterCompletion()
                    ->visible(fn() => ! auth()->user()?->isMurid()),
                DeleteBulkAction::make()
                    ->label('Hapus Terpilih')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Data Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->visible(fn() => ! auth()->user()?->isMurid()),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->deferLoading()
            ->persistFiltersInSession()
            ->headerActions([
                Action::make('advisor_chat')
                    ->label('Diskusi Pembimbing')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->modalHeading('Grup Diskusi Pembimbingan')
                    ->modalContent(function () {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        $dosenId = null;

                        if ($user?->isPengajar()) {
                            $dosenId = $user->getDosenId();
                        } elseif ($user?->isMurid()) {
                            // Ambil dosen wali dari riwayat pendidikan terbaru
                            $dosenId = $user->siswaData?->riwayatPendidikan()
                                ->whereNotNull('id_wali_dosen')
                                ->orderBy('id', 'desc')
                                ->first()?->id_wali_dosen;
                        } elseif ($user?->hasRole('super_admin')) {
                            $dosenId = 'admin_select'; // Flag for livewire component
                        }

                        if (!$dosenId) return view('filament.components.empty-chat');

                        return view('filament.resources.akademik-krs.actions.chat-modal', [
                            'dosenId' => $dosenId,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('4xl')
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user && ($user->isMurid() || $user->isPengajar() || $user->hasRole('super_admin'));
                    })
                    ->badge(function () {
                        return null;
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
