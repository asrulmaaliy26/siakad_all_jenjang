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
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;

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
                    ->color('primary'),

                TextColumn::make('riwayatPendidikan.siswaData.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('NIM berhasil disalin')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-clipboard')
                    ->iconPosition('after'),

                // Data KRS
                TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state) => "Semester {$state}")
                    ->icon('heroicon-o-academic-cap')
                    ->iconPosition('before'),

                TextColumn::make('jumlah_sks')
                    ->label('SKS')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state >= 20 ? 'success' : ($state >= 15 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn($state) => "{$state} SKS")
                    ->icon('heroicon-o-calculator')
                    ->iconPosition('before'),

                // Status Bayar dengan SelectColumn yang mendukung dark mode
                SelectColumn::make('status_bayar')
                    ->label('Status Bayar')
                    ->options([
                        'Y' => 'Lunas',
                        'N' => 'Belum Lunas',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
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
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                    ])
                    ->selectablePlaceholder(false)
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
                    ->extraAttributes(function ($state) {
                        $classes = [
                            'Y' => 'status-badge status-active',
                            'N' => 'status-badge status-inactive',
                        ];
                        return ['class' => $classes[$state] ?? 'status-badge status-default'];
                    }),

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
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options([
                        '2023/2024' => '2023/2024',
                        '2024/2025' => '2024/2025',
                    ])
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
                    ->modalWidth('7xl'),

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
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                DeleteBulkAction::make()
                    ->label('Hapus Terpilih')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Data Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->deferLoading()
            ->persistFiltersInSession()
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
