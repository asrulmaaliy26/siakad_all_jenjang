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
                //
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
                \Filament\Actions\Action::make('download_template')
                    ->label('Template Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn() => \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MataPelajaranKurikulumTemplateExport, 'template_mapel_distribusi.xlsx')),
                \Filament\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\MataPelajaranKurikulumImporter::class),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
