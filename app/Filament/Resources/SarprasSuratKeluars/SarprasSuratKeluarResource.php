<?php

namespace App\Filament\Resources\SarprasSuratKeluars;

use App\Filament\Resources\SarprasSuratKeluars\Pages\CreateSarprasSuratKeluar;
use App\Filament\Resources\SarprasSuratKeluars\Pages\EditSarprasSuratKeluar;
use App\Filament\Resources\SarprasSuratKeluars\Pages\ListSarprasSuratKeluars;
use App\Filament\Resources\SarprasSuratKeluars\Schemas\SarprasSuratKeluarForm;
use App\Filament\Resources\SarprasSuratKeluars\Tables\SarprasSuratKeluarsTable;
use App\Models\SarprasSuratKeluar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;

class SarprasSuratKeluarResource extends Resource
{
    protected static ?string $model = SarprasSuratKeluar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static string|\UnitEnum|null $navigationGroup = 'Sarpras / Inventaris';
    protected static ?string $navigationLabel = 'Surat Keluar';
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'nomor_surat';

    public static function form(Schema $schema): Schema
    {
        return SarprasSuratKeluarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SarprasSuratKeluarsTable::configure($table)
            ->actions([
                \Filament\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('sarpras.surat-keluar.cetak', $record->id))
                    ->openUrlInNewTab(),
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSarprasSuratKeluars::route('/'),
            'create' => CreateSarprasSuratKeluar::route('/create'),
            'edit' => EditSarprasSuratKeluar::route('/{record}/edit'),
        ];
    }
}
