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
use App\Models\TahunAkademik;

class AkademikKrsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),

                // Relasi / Foreign Key
                TextColumn::make('riwayatPendidikan.siswaData.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->color('primary')
                    ->size('sm')
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
                    ->size('sm')
                    ->toggleable(),

                TextColumn::make('riwayatPendidikan.waliDosen.nama')
                    ->label('Wali Dosen')
                    ->searchable()
                    ->sortable()
                    ->size('sm')
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
                        return $record->riwayatPendidikan?->getSemester(null, $record->id_tahun_akademik);
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
                    ->formatStateUsing(fn($record) => $record->tahunAkademik
                        ? "{$record->tahunAkademik->nama} - {$record->tahunAkademik->periode}"
                        : '-')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('sm')
                    ->toggleable(),

                // Status Bayar dengan SelectColumn yang mendukung dark mode
                TextColumn::make('status_bayar')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Y' => 'success',
                        'N' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'Y' => 'Lunas',
                        'N' => 'Belum Lunas',
                        default => $state,
                    })
                    ->sortable()
                    ->action(
                        \Filament\Actions\Action::make('edit_status_bayar')
                            ->modalHeading('Update Status Bayar')
                            ->form([
                                \Filament\Forms\Components\Select::make('status_bayar')
                                    ->label('Status Bayar')
                                    ->options([
                                        'Y' => 'Lunas',
                                        'N' => 'Belum Lunas',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->status_bayar)
                            ])
                            ->action(function ($record, array $data) {
                                $record->update(['status_bayar' => $data['status_bayar']]);
                            })
                            ->visible(function () {
                                $user = auth()->user();
                                return ! ($user && ($user->isMurid() || $user->isPengajar()));
                            })
                    ),


                // Syarat UTS dengan SelectColumn
                TextColumn::make('syarat_uts')
                    ->label('Syarat UTS')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Y' => 'success',
                        'N' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                        default => $state,
                    })
                    ->action(
                        \Filament\Actions\Action::make('edit_syarat_uts')
                            ->modalHeading('Update Syarat UTS')
                            ->form([
                                \Filament\Forms\Components\Select::make('syarat_uts')
                                    ->label('Syarat UTS')
                                    ->options([
                                        'Y' => 'Terpenuhi',
                                        'N' => 'Belum',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->syarat_uts)
                            ])
                            ->action(function ($record, array $data) {
                                $record->update(['syarat_uts' => $data['syarat_uts']]);
                            })
                            ->visible(fn() => ! (auth()->user()?->isMurid() || auth()->user()?->isPengajar()))
                    ),


                // Syarat UAS dengan SelectColumn
                TextColumn::make('syarat_uas')
                    ->label('Syarat UAS')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Y' => 'success',
                        'N' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                        default => $state,
                    })
                    ->action(
                        \Filament\Actions\Action::make('edit_syarat_uas')
                            ->modalHeading('Update Syarat UAS')
                            ->form([
                                \Filament\Forms\Components\Select::make('syarat_uas')
                                    ->label('Syarat UAS')
                                    ->options([
                                        'Y' => 'Terpenuhi',
                                        'N' => 'Belum',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->syarat_uas)
                            ])
                            ->action(function ($record, array $data) {
                                $record->update(['syarat_uas' => $data['syarat_uas']]);
                            })
                            ->visible(fn() => ! (auth()->user()?->isMurid() || auth()->user()?->isPengajar()))
                    ),


                // Syarat KRS dengan SelectColumn
                TextColumn::make('syarat_krs')
                    ->label('Syarat KRS')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Y' => 'success',
                        'N' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'Y' => 'Disetujui',
                        'N' => 'Menunggu Persetujuan',
                        default => $state,
                    })
                    ->action(
                        \Filament\Actions\Action::make('edit_syarat_krs')
                            ->modalHeading('Update Syarat KRS')
                            ->form([
                                \Filament\Forms\Components\Select::make('syarat_krs')
                                    ->label('Syarat KRS')
                                    ->options([
                                        'Y' => 'Disetujui',
                                        'N' => 'Menunggu Persetujuan',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->syarat_krs)
                            ])
                            ->action(function ($record, array $data) {
                                $record->update(['syarat_krs' => $data['syarat_krs']]);
                            })
                            ->visible(fn() => ! auth()->user()?->isMurid())
                    ),


                // Status Aktif dengan SelectColumn
                TextColumn::make('status_aktif')
                    ->label('Status Aktif')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Y' => 'success',
                        'N' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'Y' => 'Aktif',
                        'N' => 'Tidak Aktif',
                        default => $state,
                    })
                    ->action(
                        \Filament\Actions\Action::make('edit_status_aktif')
                            ->modalHeading('Update Status Aktif')
                            ->form([
                                \Filament\Forms\Components\Select::make('status_aktif')
                                    ->label('Status Aktif')
                                    ->options([
                                        'Y' => 'Aktif',
                                        'N' => 'Tidak Aktif',
                                    ])
                                    ->required()
                                    ->default(fn($record) => $record->status_aktif)
                            ])
                            ->action(function ($record, array $data) {
                                $state = $data['status_aktif'];
                                if ($state === 'N' && $record->status_aktif !== 'N') {
                                    // Cek syarat bayar
                                    if ($record->status_bayar !== 'Y') {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Gagal Menonaktifkan')
                                            ->body('KRS tidak dapat dinonaktifkan karena status pembayaran belum disetujui atau belum lunas.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }

                                    // Cek status mahasiswa (Aktif/Tidak)
                                    $statusMhs = $record->riwayatPendidikan?->statusSiswa?->nilai ?? 'Tidak Diketahui';
                                    if (strtolower($statusMhs) !== 'aktif') {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Gagal Menonaktifkan')
                                            ->body("KRS tidak dapat dinonaktifkan karena status Mahasiswa saat ini adalah: {$statusMhs}. Mahasiswa harus berstatus 'Aktif'.")
                                            ->warning()
                                            ->send();
                                        return;
                                    }

                                    try {
                                        // Update state temporarily so it triggers the logic
                                        $record->update(['status_aktif' => 'N']);
                                        $record->deactivateAndCreateNew();
                                        \Filament\Notifications\Notification::make()
                                            ->title('Berhasil')
                                            ->body('KRS telah dinonaktifkan dan KRS baru untuk semester berikutnya telah dibuat otomatis.')
                                            ->success()
                                            ->send();
                                    } catch (\Exception $e) {
                                        // Revert status_aktif jika gagal
                                        $record->update(['status_aktif' => 'Y']);
                                        \Filament\Notifications\Notification::make()
                                            ->title('Gagal')
                                            ->body($e->getMessage())
                                            ->danger()
                                            ->persistent()
                                            ->send();
                                    }
                                } elseif ($state === 'Y' && $record->status_aktif !== 'Y') {
                                    $record->update(['status_aktif' => 'Y']);
                                }
                            })
                            ->visible(fn() => ! auth()->user()?->isMurid())
                    ),

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

                            // Logika filter ini disinkronkan dengan RiwayatPendidikan::getSemester()
                            // Semester = (refPeriod - startPeriod) + 1
                            // refPeriod = YEAR(refDate) * 2 + IF(MONTH(refDate) <= 6, 0, 1)
                            // startPeriod = YEAR(tanggal_mulai) * 2 + IF(MONTH(tanggal_mulai) <= 6, 0, 1)

                            $q->whereRaw("
                                (
                                    (YEAR(COALESCE(akademik_krs.tgl_krs, akademik_krs.created_at)) * 2 + IF(MONTH(COALESCE(akademik_krs.tgl_krs, akademik_krs.created_at)) <= 6, 0, 1))
                                    - 
                                    (YEAR(tanggal_mulai) * 2 + IF(MONTH(tanggal_mulai) <= 6, 0, 1))
                                    + 1
                                ) = ?
                            ", [$targetSemester]);
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(fn() => \App\Models\Jurusan::pluck('nama', 'id')->toArray())
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return;
                        $query->whereHas('riwayatPendidikan', function ($q) use ($data) {
                            $q->where('id_jurusan', $data['value']);
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(fn() => \App\Models\TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
                    ->default(\App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
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

                SelectFilter::make('id_wali_dosen')
                    ->label('Wali Dosen')
                    ->options(fn() => \App\Models\DosenData::pluck('nama', 'id')->toArray())
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return;
                        $query->whereHas('riwayatPendidikan', function ($q) use ($data) {
                            $q->where('id_wali_dosen', $data['value']);
                        });
                    })
                    ->default(fn() => auth()->user()?->getDosenId())
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->headerActions([])
            ->actions([
                Action::make('lanjutkan_studi')
                    ->label('Lanjutkan Studi')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('warning')
                    ->button()
                    ->modalHeading('Lanjutkan Studi Mahasiswa')
                    ->modalDescription('KRS semester ini akan dinonaktifkan dan KRS baru akan dibuat untuk Tahun Akademik yang dipilih.')
                    ->modalSubmitActionLabel('Lanjutkan Studi')
                    ->modalCancelActionLabel('Batal')
                    ->form([
                        Select::make('target_ta_id')
                            ->label('Tahun Akademik Tujuan')
                            ->options(
                                TahunAkademik::orderByDesc('id')
                                    ->get()
                                    ->mapWithKeys(fn($ta) => [$ta->id => "{$ta->nama} - {$ta->periode}"])
                            )
                            ->default(fn() => TahunAkademik::orderByDesc('id')->first()?->id)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->placeholder('Pilih Tahun Akademik...'),
                    ])
                    ->action(function ($record, array $data) {
                        try {
                            $record->lanjutkanStudi((int) $data['target_ta_id']);

                            $nama = $record->riwayatPendidikan?->siswaData?->nama ?? 'Mahasiswa';
                            Notification::make()
                                ->title('Studi Dilanjutkan')
                                ->body("KRS baru untuk {$nama} berhasil dibuat.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Melanjutkan Studi')
                                ->body($e->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        if ($user->isMurid()) return false;
                        if ($record->status_aktif !== 'Y') return false;

                        // Admin bisa untuk semua mahasiswa
                        if ($user->isAdmin()) return true;

                        // Wali Dosen hanya bisa untuk waliannya sendiri
                        if ($user->isPengajar()) {
                            $dosenId = $user->getDosenId();
                            return $dosenId
                                && $record->riwayatPendidikan?->id_wali_dosen == $dosenId;
                        }

                        return false;
                    }),

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

                    Action::make('cetak_transkrip')
                        ->label('Cetak Transkrip')
                        ->icon('heroicon-o-document-text')
                        ->color('primary')
                        ->url(fn($record) => route('cetak.transkrip', ['id' => $record->id_riwayat_pendidikan]))
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
                    ->dropdownPlacement('bottom-start')
            ], position: \Filament\Tables\Enums\RecordActionsPosition::BeforeCells)
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
                            ->placeholder('Pilih Status Aktif...')
                            ->disabled(fn() => auth()->user()?->isPengajar()),
                        Select::make('status_bayar')
                            ->label('Status Bayar')
                            ->options([
                                'Y' => 'Lunas',
                                'N' => 'Belum Lunas',
                            ])
                            ->placeholder('Pilih Status Bayar...')
                            ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar()),
                        Select::make('syarat_uts')
                            ->label('Syarat UTS')
                            ->options([
                                'Y' => 'Terpenuhi',
                                'N' => 'Belum Terpenuhi',
                            ])
                            ->placeholder('Pilih Syarat UTS...')
                            ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar()),
                        Select::make('syarat_uas')
                            ->label('Syarat UAS')
                            ->options([
                                'Y' => 'Terpenuhi',
                                'N' => 'Belum Terpenuhi',
                            ])
                            ->placeholder('Pilih Syarat UAS...')
                            ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar()),
                        Select::make('syarat_krs')
                            ->label('Syarat KRS')
                            ->options([
                                'Y' => 'Disetujui',
                                'N' => 'Menunggu Persetujuan',
                            ])
                            ->placeholder('Pilih Syarat KRS...')
                            ->disabled(fn() => auth()->user()?->isMurid()),
                            
                        Select::make('id_tahun_akademik')
                            ->label('Tahun Akademik (Koreksi)')
                            ->options(
                                \App\Models\TahunAkademik::orderByDesc('id')
                                    ->get()
                                    ->mapWithKeys(fn($ta) => [$ta->id => "{$ta->nama} - {$ta->periode}"])
                            )
                            ->placeholder('Pilih Tahun Akademik...')
                            ->searchable()
                            ->disabled(fn() => auth()->user()?->isMurid()),
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
            ->paginated([10, 25, 50, 100, 250, 'all'])
            ->defaultPaginationPageOption(25)
            ->headerActions([
                Action::make('advisor_chat')
                    ->label('Diskusi Pembimbing')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->modalHeading('Grup Diskusi Pembimbingan')
                    ->modalContent(function () {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        $dosenId = $user?->getDosenId();

                        if (!$dosenId && $user?->isMurid()) {
                            // Ambil dosen wali dari riwayat pendidikan terbaru
                            $dosenId = $user->siswaData?->riwayatPendidikan()
                                ->whereNotNull('id_wali_dosen')
                                ->orderBy('id', 'desc')
                                ->first()?->id_wali_dosen;
                        } elseif (!$dosenId && $user?->hasRole('super_admin')) {
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
