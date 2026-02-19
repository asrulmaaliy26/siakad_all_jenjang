<?php

namespace App\Filament\Resources\Kelas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\TahunAkademik;
use App\Models\JenjangPendidikan;
use App\Models\Jurusan;
use App\Models\ProgramKelas;
use Filament\Actions\ViewAction;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('programKelas.nilai') // memanggil relasi programKelas di model Kelas
                    ->label('Program Kelas')
                    ->sortable()
                    ->searchable(),
                // TextColumn::make('nama'),
                TextColumn::make('semester')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jurusan.nama')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jurusan.jenjangPendidikan.nama')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tahunAkademik.nama')
                    ->numeric()
                    ->sortable(),
                // TextColumn::make('status_aktif'),
                ToggleColumn::make('status_aktif')
                    ->label('Status')
                    // ->getStateUsing(fn($record) => $record->status === 'Y')
                    ->updateStateUsing(function ($state, $record) {
                        $record->update([
                            'status' => $state ? 'Y' : 'N',
                        ]);
                    })
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            /* =========================
             * FILTER SELECT
             * ========================= */
            ->filters([

                SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(
                        TahunAkademik::pluck('nama', 'id')
                    )
                    ->searchable(),

                SelectFilter::make('jenjang_pendidikan') // Changed name to avoid conflict with missing column
                    ->label('Jenjang Pendidikan')
                    ->options(
                        JenjangPendidikan::pluck('nama', 'id')
                    )
                    ->query(function ($query, array $data) {
                        return $query->when($data['value'], function ($query, $value) {
                            $query->whereHas('jurusan', function ($query) use ($value) {
                                $query->where('id_jenjang_pendidikan', $value);
                            });
                        });
                    })
                    ->searchable(),

                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(
                        Jurusan::pluck('nama', 'id')
                    )
                    ->searchable(),

                // SelectFilter::make('id_program_kelas')
                //     ->label('Program Kelas')
                //     ->options(
                //         ProgramKelas::pluck('nama', 'id')
                //     )
                //     ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            // ->toolbarActions([])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
