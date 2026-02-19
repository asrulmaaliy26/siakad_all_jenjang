<?php

namespace App\Filament\Resources\DosenData\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Jurusan;
use App\Models\RefOption\PangkatGolongan;
use App\Models\RefOption\JabatanFungsional;
use App\Models\RefOption\StatusDosen;
use App\Models\RefOption\Agama;
use Filament\Actions\ViewAction;


class DosenDataTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_lahir')
                    ->label('TTL')
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('NIY')
                    ->label('NIY')
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('NIPDN')
                    ->label('NIPDN')
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('jurusan.nama')
                    ->label('Home Base')
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('jabatanFungsional.nilai')
                    ->label('Jabatan Fungsional')
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('pangkat.nilai')
                    ->label('Pangkat Golongan')
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('statusDosen.nilai')
                    ->label('Status Dosen')
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(Jurusan::pluck('nama', 'id'))
                    ->searchable(),

                SelectFilter::make('ro_pangkat_gol')
                    ->label('Pangkat Golongan')
                    ->options(PangkatGolongan::pluck('nilai', 'id'))
                    ->searchable(),

                SelectFilter::make('ro_jabatan')
                    ->label('Jabatan Fungsional')
                    ->options(JabatanFungsional::pluck('nilai', 'id'))
                    ->searchable(),

                SelectFilter::make('ro_status_dosen')
                    ->label('Status Dosen')
                    ->options(StatusDosen::pluck('nilai', 'id'))
                    ->searchable(),

                SelectFilter::make('ro_agama')
                    ->label('Agama')
                    ->options(Agama::pluck('nilai', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
            ]);
    }
}
