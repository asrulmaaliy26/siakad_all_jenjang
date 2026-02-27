<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Tables;

use App\Filament\Resources\MataPelajaranKelasDistribusis\Actions\ExportMataPelajaranKelasAction;
use App\Filament\Resources\MataPelajaranKelasDistribusis\Actions\ImportMataPelajaranKelasAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class MataPelajaranKelasDistribusisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: 'mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                //  TextColumn::make('id_mata_pelajaran_kurikulum')
                //     ->label('ID Kurikulum')
                //     ->numeric()
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('kelas.programKelas.nilai')
                    ->label('Program Kelas')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.kode_feeder')
                    ->label('Kode MK')
                    ->toggleable(),

                TextColumn::make('dosen.nama')
                    ->label('Dosen')
                    ->sortable()
                    ->toggleable(),

                // TextColumn::make('pengawas.nama')
                //     ->label('Pengawas')
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('ruangKelas.nilai')
                    ->label('Ruang')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('pelaksanaanKelas.nilai')
                    ->label('Pelaksanaan')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('hari')
                    ->label('Hari')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    // ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jam')
                    ->label('Jam')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('uts')
                    ->label('Jadwal UTS')
                    // ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('uas')
                    ->label('Jadwal UAS')
                    // ->dateTime()
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('status_uts')
                    ->label('Status UTS')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ])
                    ->toggleable(),

                BadgeColumn::make('status_uas')
                    ->label('Status UAS')
                    ->colors([
                        'success' => 'aktif',
                        'danger'  => 'nonaktif',
                    ])
                    ->toggleable(),

                TextColumn::make('ruang_uts')
                    ->label('Ruang UTS')
                    ->toggleable(),

                TextColumn::make('ruang_uas')
                    ->label('Ruang UAS')
                    ->toggleable(),

                TextColumn::make('link_kelas')
                    ->label('Link Kelas')
                    ->url(fn($record) => $record->link_kelas ?: null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('passcode')
                    ->label('Passcode')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter berdasarkan Mata Pelajaran
                SelectFilter::make('mata_pelajaran')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaranKurikulum.mataPelajaranMaster', 'nama')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Program Kelas
                SelectFilter::make('program_kelas')
                    ->label('Program Kelas')
                    ->relationship('kelas.programKelas', 'nilai')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Dosen
                SelectFilter::make('dosen')
                    ->label('Dosen')
                    ->relationship('dosen', 'nama')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Hari
                SelectFilter::make('hari')
                    ->label('Hari')
                    ->options([
                        'Senin'  => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu'   => 'Rabu',
                        'Kamis'  => 'Kamis',
                        'Jumat'  => 'Jumat',
                        'Sabtu'  => 'Sabtu',
                        'Minggu' => 'Minggu',
                    ]),

                // Filter Status UTS
                SelectFilter::make('status_uts')
                    ->label('Status UTS')
                    ->options(['Y' => 'Aktif (Y)', 'N' => 'Nonaktif (N)']),

                // Filter Status UAS
                SelectFilter::make('status_uas')
                    ->label('Status UAS')
                    ->options(['Y' => 'Aktif (Y)', 'N' => 'Nonaktif (N)']),

                // Filter Tanggal range
                Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('tanggal_dari')
                            ->label('Tanggal Dari'),
                        \Filament\Forms\Components\DatePicker::make('tanggal_sampai')
                            ->label('Tanggal Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_dari'],
                                fn(Builder $q, $date) => $q->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_sampai'],
                                fn(Builder $q, $date) => $q->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->columns(2),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Export baris terpilih dengan pilihan kolom
                    ExportMataPelajaranKelasAction::makeBulk(),
                    // Export bawaan pxlrbt (semua kolom cepat)
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
                // Export semua data (dengan filter aktif) — pilih kolom
                ExportMataPelajaranKelasAction::make(),

                // Import / Update data — langsung diproses tanpa queue
                ImportMataPelajaranKelasAction::make(),
            ]);
    }
}
