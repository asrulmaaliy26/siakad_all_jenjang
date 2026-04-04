<?php

namespace App\Filament\Resources\SarprasSuratKeluars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class SarprasSuratKeluarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nomor_surat')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('kategori.nama')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('tahunAkademik.nama')
                    ->label('Th. Akademik')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('perihal')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('tanggal_surat')
                    ->date()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Sent' => 'success',
                        'Archived' => 'info',
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('tahun_akademik_id')
                    ->relationship('tahunAkademik', 'nama')
                    ->default(\App\Models\TahunAkademik::where('status', 'Y')->first()?->id),
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
