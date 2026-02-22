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

                TextColumn::make('jurusan.jenjangPendidikan.nama')
                    ->label('Jenjang')
                    ->badge()
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('Tgl_Daftar')
                    ->label('Tgl Daftar')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('success'),

                TextColumn::make('Tahun_Masuk')
                    ->label('Tahun Masuk')
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
                    ->disabled(fn() => auth()->user()->isMurid()),

                TextColumn::make('reff')
                    ->label('Referral')
                    ->badge()
                    ->color('purple')
                    ->icon('heroicon-o-link')
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

                    ->disabled(fn() => auth()->user()->isMurid()),

                SelectColumn::make('Status_Kelulusan')
                    ->label('Status Kelulusan')
                    ->options([
                        'B' => '⏳ Proses',
                        'Y' => '🎓 Lulus',
                        'N' => '❌ Tidak Lulus',
                    ])
                    ->sortable()
                    ->disabled(fn() => auth()->user()->isMurid()),

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
                    ->relationship('programSekolahRef', 'nilai')
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
                    ->multiple(),

                SelectFilter::make('Status_Kelulusan')
                    ->label('Status Kelulusan')
                    ->options([
                        'B' => 'Proses',
                        'Y' => 'Lulus',
                        'N' => 'Tidak Lulus',
                    ])
                    ->multiple(),

                SelectFilter::make('Jalur_PMB')
                    ->label('Jalur PMB')
                    ->relationship('jalurPmbRef', 'nilai')
                    ->preload()
                    ->multiple(),

                SelectFilter::make('Tahun_Masuk')
                    ->label('Tahun Masuk')
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
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
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                ExportAction::make()
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
