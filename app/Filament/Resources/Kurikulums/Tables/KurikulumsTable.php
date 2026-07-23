<?php

namespace App\Filament\Resources\Kurikulums\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\TahunAkademik;

class KurikulumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('jurusan.nama')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tahunAkademik.nama')
                    ->numeric()
                    ->sortable(),

                // TextColumn::make('status_aktif'),
                ToggleColumn::make('status_aktif')
                    ->label('Status')
                    // ->getStateUsing(fn($record) => $record->status === 'Y')
                    ->updateStateUsing(function ($state, $record) {
                        $record->update([
                            'status' => $state ? 'Y' : 'N',
                        ]);
                    })
                    ->onColor('success')
                    ->offColor('danger'),
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
                SelectFilter::make('id_tahun_akademik')->default(fn () => session('global_tahun_akademik_id') ?? \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->label('Tahun Akademik')
                    ->options(
                        TahunAkademik::orderByDesc('id')->get()->mapWithKeys(fn($item) => [$item->id => "{$item->nama} - {$item->periode}"])
                    )
                    ->default(fn () => session('global_tahun_akademik_id') ?? TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
