<?php

namespace App\Filament\Resources\MataPelajaranKelas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MataPelajaranKelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(function ($record) {
                /** @var \App\Models\User|null $user */
                $user = \Illuminate\Support\Facades\Auth::user();

                if ($user?->isMurid() && empty($record->id_dosen_data)) {
                    return null; // Mematikan tautan klik pada baris ini
                }

                // Gunakan default navigasi ke halaman view untuk baris yang diizinkan
                return \App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource::getUrl('view', ['record' => $record]);
            })
            ->columns([
                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('kelas.semester')
                    ->label('Semester')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('hari')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('jam')
                    ->toggleable(),
                TextColumn::make('ruangKelas.nilai')
                    ->label('Ruang')
                    ->toggleable(),
                TextColumn::make('jumlah')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('status_uts')
                    ->label('UTS')
                    ->boolean()
                    ->toggleable(),
                IconColumn::make('status_uas')
                    ->label('UAS')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->disabled(function ($record) {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user?->isMurid() && empty($record->id_dosen_data);
                    })
                    ->tooltip(function ($record) {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if ($user?->isMurid() && empty($record->id_dosen_data)) {
                            return 'Belum dapat diakses, Dosen pengajar belum ditentukan.';
                        }
                        return null;
                    }),
                EditAction::make()
                    ->disabled(function () {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user?->isMurid();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make()
                        ->disabled(function () {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $user?->isMurid();
                        }),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
