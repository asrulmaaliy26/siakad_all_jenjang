<?php

namespace App\Filament\Resources\ReferenceOptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\ReferenceOption;


class ReferenceOptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('nama_grup')
                    ->label('Nama Grup')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kode')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->wrap()
                    ->sortable(),

                ToggleColumn::make('status')
                    ->label('Status')
                    // ->getStateUsing(fn($record) => $record->status === 'Y')
                    ->updateStateUsing(function ($state, $record) {
                        $record->update([
                            'status' => $state ? 'Y' : 'N',
                        ]);
                    })
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(40),
            ])
            ->filters([
                //
                SelectFilter::make('nama_grup')
                    ->label('Nama Grup')
                    ->options(
                        ReferenceOption::query()
                            ->select('nama_grup')
                            ->distinct()
                            ->orderBy('nama_grup')
                            ->pluck('nama_grup', 'nama_grup')
                            ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Semua Grup'),
            ])
            ->recordActions([
                EditAction::make(),
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
