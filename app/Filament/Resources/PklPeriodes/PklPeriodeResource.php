<?php

namespace App\Filament\Resources\PklPeriodes;

use App\Filament\Resources\PklPeriodes\Pages\CreatePklPeriode;
use App\Filament\Resources\PklPeriodes\Pages\EditPklPeriode;
use App\Filament\Resources\PklPeriodes\Pages\ListPklPeriodes;
use App\Filament\Resources\PklPeriodes\Pages\ViewPklPeriode;
use App\Filament\Resources\PklPeriodes\RelationManagers\LembagasRelationManager;
use App\Filament\Resources\PklPeriodes\Schemas\PklPeriodeForm;
use App\Filament\Resources\PklPeriodes\Tables\PklPeriodesTable;
use App\Models\PklPeriode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PklPeriodeResource extends Resource
{
    protected static ?string $model = PklPeriode::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Periode PKL';

    protected static string|UnitEnum|null $navigationGroup = 'PKL';

    protected static ?int $navigationSort = 51;

    public static function form(Schema $schema): Schema
    {
        return PklPeriodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PklPeriodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LembagasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPklPeriodes::route('/'),
            'create' => CreatePklPeriode::route('/create'),
            'view'   => ViewPklPeriode::route('/{record}'),
            'edit'   => EditPklPeriode::route('/{record}/edit'),
        ];
    }
}
