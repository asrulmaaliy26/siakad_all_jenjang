<?php

namespace App\Filament\Resources\PeriodeWisudas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PeriodeWisudasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun'),
                TextColumn::make('periode_ke')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kuota')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pendaftar_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status'),
                TextColumn::make('tanggal_pelaksanaan')
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
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
