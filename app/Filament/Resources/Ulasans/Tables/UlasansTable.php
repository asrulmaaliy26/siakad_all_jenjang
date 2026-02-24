<?php

namespace App\Filament\Resources\Ulasans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UlasansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pengulas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reviewable_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn($state) => str_replace('App\\Models\\', '', $state))
                    ->badge(),

                TextColumn::make('reviewable.nama')
                    ->label('Objek')
                    ->placeholder('N/A')
                    ->searchable(),

                TextColumn::make('bintang')
                    ->label('Rating')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state) . " ($state)")
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('bintang')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐',
                        4 => '⭐⭐⭐⭐',
                        3 => '⭐⭐⭐',
                        2 => '⭐⭐',
                        1 => '⭐',
                    ]),
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
