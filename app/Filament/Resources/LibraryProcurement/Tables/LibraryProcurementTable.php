<?php

namespace App\Filament\Resources\LibraryProcurement\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LibraryProcurementTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')
                    ->label('No. Ref')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vendor')
                    ->label('Vendor')
                    ->searchable(),
                TextColumn::make('procurement_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->alignment('right')
                    ->sortable(),
                TextColumn::make('staff.name')
                    ->label('Petugas'),
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
