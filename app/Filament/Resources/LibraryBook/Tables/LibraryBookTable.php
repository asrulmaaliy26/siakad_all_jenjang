<?php

namespace App\Filament\Resources\LibraryBook\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LibraryBookTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Sampul')
                    ->circular(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('author.name')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->alignment('center'),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
