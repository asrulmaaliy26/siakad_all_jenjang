<?php

namespace App\Filament\Resources\LibraryLoan\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LibraryLoanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('riwayatPendidikan.siswaData.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'returned' => 'success',
                        'borrowed' => 'info',
                        'overdue' => 'danger',
                        'lost' => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'returned' => 'Dikembalikan',
                        'borrowed' => 'Dipinjam',
                        'overdue' => 'Terlambat',
                        'lost' => 'Hilang',
                    }),
                TextColumn::make('borrowed_at')
                    ->label('Pinjam')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label('Deadline')
                    ->date()
                    ->sortable(),
                TextColumn::make('fine_amount')
                    ->label('Denda')
                    ->money('IDR')
                    ->alignment('right'),
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
