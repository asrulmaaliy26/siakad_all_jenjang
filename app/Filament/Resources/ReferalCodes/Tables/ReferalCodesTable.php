<?php

namespace App\Filament\Resources\ReferalCodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReferalCodeExport;

class ReferalCodesTable
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
                TextColumn::make('kode')
                    ->searchable(),
                TextColumn::make('type'),
                TextColumn::make('status')
                    ->searchable(),
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
                \Filament\Actions\Action::make('show_qr')
                    ->label('QR & Link')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading('QR Code & Link Pendaftaran')
                    ->modalContent(fn($record) => view('filament.components.qr-code-referral', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export_with_qr')
                        ->label('Export dengan QR')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(fn(Collection $records) => Excel::download(
                            new ReferalCodeExport($records),
                            'referal_codes_with_qr_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                        )),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
