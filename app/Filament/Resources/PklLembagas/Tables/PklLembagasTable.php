<?php

namespace App\Filament\Resources\PklLembagas\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

class PklLembagasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('nama')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kontak')
                    ->label('Kontak'),

                TextColumn::make('website')
                    ->label('Website')
                    ->limit(30),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                // bulk actions
            ]);
    }
}
