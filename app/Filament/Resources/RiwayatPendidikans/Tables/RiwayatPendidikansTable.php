<?php

namespace App\Filament\Resources\RiwayatPendidikans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\BulkAction;
use App\Models\DosenData;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Select;

class RiwayatPendidikansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('angkatan')
                    ->sortable(),
                TextColumn::make('semester')
                    ->label('Smt')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn($record) => $record->getSemester())
                    ->sortable(),
                TextColumn::make('siswa.nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jurusan.jenjangPendidikan.nama')
                    // ->numeric()
                    ->sortable(),
                TextColumn::make('jurusan.nama')
                    // ->numeric()
                    ->sortable(),
                TextColumn::make('waliDosen.nama')
                    ->label('Wali Dosen')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('statusSiswa.nilai')
                    ->label('Status Siswa')
                    ->toggleable(false)
                    ->sortable(),
                // TextColumn::make('tanggal_mulai')
                //     ->date()
                //     ->sortable(),
                ToggleColumn::make('status')
                    ->label('Status')
                    // ->getStateUsing(fn($record) => $record->status === 'Y')
                    ->updateStateUsing(function ($state, $record) {
                        // dd($record, $state);
                        $record->update([
                            'status' => $state ? 'Y' : 'N',
                        ]);
                    })
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('tanggal_selesai')
                    ->date()
                    ->sortable(),
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
                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama'),
            ])
            ->recordActions([
                EditAction::make(),
                VIewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('set_wali_dosen')
                        ->label('Set Wali Dosen')
                        ->icon('heroicon-o-user-group')
                        ->form([
                            Select::make('id_wali_dosen')
                                ->label('Pilih Wali Dosen')
                                ->options(DosenData::pluck('nama', 'id'))
                                ->placeholder('Pilih Dosen...')
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update([
                                'id_wali_dosen' => $data['id_wali_dosen'],
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
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
