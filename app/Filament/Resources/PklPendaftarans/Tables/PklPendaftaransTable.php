<?php

namespace App\Filament\Resources\PklPendaftarans\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Support\Facades\Auth;

class PklPendaftaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswaData.nama')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->visible(fn() => Auth::user()->isAdmin()),

                TextColumn::make('periode.nama')
                    ->label('Periode'),

                TextColumn::make('lembaga.nama')
                    ->label('Lembaga')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'gray',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('tgl_daftar')
                    ->label('Tgl Daftar')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn() => Auth::user()->isAdmin()),
                    DeleteAction::make()
                        ->visible(fn() => Auth::user()->isAdmin()),
                ]),
            ]);
    }
}
