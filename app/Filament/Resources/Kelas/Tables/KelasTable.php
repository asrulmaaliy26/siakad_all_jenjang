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
use App\Models\Jurusan;
use Filament\Actions\ViewAction;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('programKelas.nilai')
                    ->label('Program Kelas')
                    ->sortable()
                    ->searchable()
                    ->description(fn($record) => $record->kode_pddikti ? "Kode: {$record->kode_pddikti}" : 'Kode: Belum diset'),
                TextColumn::make('semester')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jurusan.nama')
                    ->sortable()
                    ->description(fn($record) => $record->jurusan && $record->jurusan->kode_prodi ? "Kode Prodi: {$record->jurusan->kode_prodi}" : 'Kode Prodi: Belum diset'),
                TextColumn::make('tahunAkademik.nama')
                    ->sortable()
                    ->description(fn($record) => $record->tahunAkademik && $record->tahunAkademik->kode_pddikti ? "Kode Smt: {$record->tahunAkademik->kode_pddikti}" : 'Kode Smt: Belum diset'),
                ToggleColumn::make('status_aktif')
                    ->label('Status')
                    ->updateStateUsing(function ($state, $record) {
                        $record->update([
                            'status_aktif' => $state ? 'Y' : 'N',
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
            ->filters([
                SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(
                        TahunAkademik::orderByDesc('id')->get()->mapWithKeys(fn($item) => [$item->id => "{$item->nama} - {$item->periode}"])
                    )
                    ->default(TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->searchable(),

                SelectFilter::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(
                        Jurusan::pluck('nama', 'id')
                    )
                    ->searchable(),
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
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
