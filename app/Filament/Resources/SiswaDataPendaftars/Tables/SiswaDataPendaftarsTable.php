<?php

namespace App\Filament\Resources\SiswaDataPendaftars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Support\Facades\Auth;

class SiswaDataPendaftarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('No'),
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary'),

                TextColumn::make('programSekolahRef.nilai')
                    ->label('Program')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'MA' => 'warning',
                        'S1' => 'success',
                        'S2' => 'info',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),



                TextColumn::make('Tgl_Daftar')
                    ->label('Tgl Daftar')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('success'),

                TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('Biaya_Pendaftaran')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->icon('heroicon-o-banknotes')
                    ->iconColor('warning')
                    ->toggleable(),

                SelectColumn::make('status_valid')
                    ->label('Status Validasi')
                    ->options([
                        '0' => '❌ Belum Divalidasi',
                        '1' => '✅ Sudah Divalidasi',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->disabled(fn() => Auth::user()?->isMurid() || Auth::user()?->isPendaftar()),



                TextColumn::make('referalCode.kode')
                    ->label('Kode Referal')
                    ->badge()
                    ->color('purple')
                    ->icon('heroicon-o-gift')
                    ->default('-')
                    ->toggleable(),

                SelectColumn::make('Status_Pendaftaran')
                    ->label('Status Pendaftaran')
                    ->options([
                        'B' => '⏳ Pending/Proses',
                        'Y' => '✅ Diterima',
                        'N' => '❌ Ditolak',
                    ])
                    ->sortable()
                    ->updateStateUsing(function ($record, $state) {
                        if ($state === 'Y') {
                            if ($record->status_valid != 1 || $record->Status_Kelulusan_Seleksi !== 'Y') {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Aksi Ditolak')
                                    ->body('Tidak dapat diterima: Pastikan Status Validasi "Sudah" dan Kelulusan Seleksi "Lulus" terlebih dahulu.')
                                    ->send();

                                // Kembalikan nilai ke asalnya agar tampilan kembali seperti semula
                                return $record->Status_Pendaftaran;
                            }
                        }

                        $record->update(['Status_Pendaftaran' => $state]);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Status berhasil diperbarui')
                            ->send();

                        return $state;
                    })
                    ->disabled(fn() => Auth::user()?->isMurid() || Auth::user()?->isPendaftar()),


                SelectColumn::make('Status_Kelulusan_Seleksi')
                    ->label('Status Kelulusan Seleksi')
                    ->options([
                        'B' => '⏳ Proses',
                        'Y' => '🎓 Lulus',
                        'N' => '❌ Tidak Lulus',
                    ])
                    ->sortable()
                    ->disabled(fn() => Auth::user()?->isMurid() || Auth::user()?->isPendaftar()),


                TextColumn::make('Diterima_di_Prodi')
                    ->label('Diterima di Prodi')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-academic-cap')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('Prodi_Pilihan_1')
                    ->label('Prodi Pilihan 1')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('jalurPmbRef.nilai')
                    ->label('Jalur PMB')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'Reguler' => 'gray',
                        'Prestasi' => 'success',
                        'Beasiswa' => 'warning',
                        'Pindahan' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('ro_program_sekolah')
                    ->label('Program Sekolah')
                    ->relationship('programSekolahRef', 'nilai', fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('nama_grup', 'program_sekolah'))
                    ->preload()
                    ->multiple(),

                SelectFilter::make('status_valid')
                    ->label('Status Validasi')
                    ->options([
                        '0' => 'Belum Divalidasi',
                        '1' => 'Sudah Divalidasi',
                    ]),

                SelectFilter::make('Status_Pendaftaran')
                    ->label('Status Pendaftaran')
                    ->options([
                        'B' => 'Pending/Proses',
                        'Y' => 'Diterima',
                        'N' => 'Ditolak',
                    ])
                    ->default(['B'])
                    ->multiple(),

                SelectFilter::make('Status_Kelulusan_Seleksi')
                    ->label('Status Kelulusan Seleksi')
                    ->options([
                        'B' => 'Proses',
                        'Y' => 'Lulus',
                        'N' => 'Tidak Lulus',
                    ])
                    ->multiple(),

                SelectFilter::make('Jalur_PMB')
                    ->label('Jalur PMB')
                    ->relationship('jalurPmbRef', 'nilai', fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('nama_grup', 'jalur_pmb'))
                    ->preload()
                    ->multiple(),

                \App\Traits\HasGlobalTahunAkademikFilter::getGlobalTahunAkademikFilter('id_tahun_akademik', true),

                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama', fn(\Illuminate\Database\Eloquent\Builder $query) => $query->where('nama', 'NOT LIKE', '%temp%'))
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->color('info'),
                EditAction::make()
                    ->iconButton()
                    ->color('warning'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('ubah_tahun_akademik')
                        ->label('Ubah Tahun Akademik')
                        ->icon('heroicon-o-calendar')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            \Filament\Forms\Components\Select::make('id_tahun_akademik')
                                ->label('Pilih Tahun Akademik Baru')
                                ->options(\App\Models\TahunAkademik::all()->mapWithKeys(fn($ta) => [$ta->id => "{$ta->nama} - {$ta->periode}"]))
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['id_tahun_akademik' => $data['id_tahun_akademik']]);
                                if ($record->siswa && $record->siswa->riwayatPendidikanAktif) {
                                    $record->siswa->riwayatPendidikanAktif->update([
                                        'id_tahun_akademik' => $data['id_tahun_akademik']
                                    ]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                ExportAction::make()
            ])
            ->striped()
            ->paginated([10, 25, 50, 100, 250, 'all']);
    }
}
