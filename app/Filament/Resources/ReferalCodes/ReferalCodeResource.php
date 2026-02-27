<?php

namespace App\Filament\Resources\ReferalCodes;

use App\Filament\Resources\ReferalCodes\Pages\CreateReferalCode;
use App\Filament\Resources\ReferalCodes\Pages\EditReferalCode;
use App\Filament\Resources\ReferalCodes\Pages\ListReferalCodes;
use App\Filament\Resources\ReferalCodes\Pages\ViewReferalCode;
use App\Filament\Resources\ReferalCodes\Schemas\ReferalCodeForm;
use App\Filament\Resources\ReferalCodes\Schemas\ReferalCodeInfolist;
use App\Filament\Resources\ReferalCodes\Tables\ReferalCodesTable;
use App\Filament\Resources\ReferalCodes\RelationManagers\PendaftarsRelationManager;
use App\Models\ReferalCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReferalCodeResource extends Resource
{
    protected static ?string $model = ReferalCode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return ReferalCodeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReferalCodeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReferalCodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PendaftarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReferalCodes::route('/'),
            'create' => CreateReferalCode::route('/create'),
            'view' => ViewReferalCode::route('/{record}'),
            'edit' => EditReferalCode::route('/{record}/edit'),
        ];
    }
}
