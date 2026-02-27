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

                TextColumn::make('objek')
                    ->label('Objek Ulasan')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tidak ada'),

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
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make()->disabled(fn() => auth()->user()?->isMurid()),
                ]),
            ]);
    }
}
