<?php

namespace App\Filament\Resources\SarprasSuratKategoris\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class SarprasSuratKategorisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                \Filament\Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('format_nomor')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
