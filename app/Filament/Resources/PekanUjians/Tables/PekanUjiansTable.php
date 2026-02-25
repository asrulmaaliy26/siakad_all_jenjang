<?php

namespace App\Filament\Resources\PekanUjians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;

class PekanUjiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->formatStateUsing(fn($record) => $record->tahunAkademik ? "{$record->tahunAkademik->nama} - {$record->tahunAkademik->periode}" : '-')
                    ->sortable()
                    ->searchable(),

                SelectColumn::make('jenis_ujian')
                    ->label('Jenis Ujian')
                    ->options([
                        'UTS' => 'UTS',
                        'UAS' => 'UAS',
                    ])
                    ->sortable()
                    ->searchable()
                    ->disabled(fn() => auth()->user()->isMurid()),

                ToggleColumn::make('status_akses')
                    ->label('Status Akses')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(fn() => auth()->user()?->isMurid()),

                ToggleColumn::make('status_bayar')
                    ->label('Syarat Pembayaran')
                    ->onColor('success')
                    ->offColor('warning')
                    ->disabled(fn() => auth()->user()?->isMurid()),

                ToggleColumn::make('status_ujian')
                    ->label('Status Aktif')
                    ->onColor('success')
                    ->offColor('gray')
                    ->disabled(fn() => auth()->user()?->isMurid()),

                TextColumn::make('informasi')
                    ->label('Informasi')
                    ->limit(50)
                    ->wrap(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(\App\Models\TahunAkademik::all()->mapWithKeys(fn($t) => [$t->id => "{$t->nama} - {$t->periode}"]))
                    ->default(\App\Models\TahunAkademik::where('status', 'Aktif')->first()?->id)
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
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
