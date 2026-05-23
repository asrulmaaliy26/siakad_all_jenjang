<?php

namespace App\Filament\Resources\PklLembagas;

use App\Filament\Resources\PklLembagas\Pages\CreatePklLembaga;
use App\Filament\Resources\PklLembagas\Pages\EditPklLembaga;
use App\Filament\Resources\PklLembagas\Pages\ListPklLembagas;
use App\Filament\Resources\PklLembagas\Pages\ViewPklLembaga;
use App\Filament\Resources\PklLembagas\Schemas\PklLembagaForm;
use App\Filament\Resources\PklLembagas\Tables\PklLembagasTable;
use App\Models\PklLembaga;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PklLembagaResource extends Resource
{
    protected static ?string $model = PklLembaga::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Lembaga PKL';

    protected static string|UnitEnum|null $navigationGroup = 'PKL';

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return PklLembagaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PklLembagasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPklLembagas::route('/'),
            'create' => CreatePklLembaga::route('/create'),
            'view'   => ViewPklLembaga::route('/{record}'),
            'edit'   => EditPklLembaga::route('/{record}/edit'),
        ];
    }
}
