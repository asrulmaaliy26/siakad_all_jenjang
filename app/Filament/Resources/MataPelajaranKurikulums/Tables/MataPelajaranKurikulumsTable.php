<?php

namespace App\Filament\Resources\MataPelajaranKurikulums\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MataPelajaranKurikulumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('kurikulum.nama')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mataPelajaranMaster.nama')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('semester')
                    ->numeric()
                    ->sortable(),
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
                \Filament\Tables\Filters\SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(\App\Models\TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $query->whereHas('kurikulum', function ($q) use ($data) {
                            $q->where('id_tahun_akademik', $data['value']);
                        });
                    })
                    ->default(fn() => \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->searchable()
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \Filament\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\MataPelajaranKurikulumImporter::class),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
