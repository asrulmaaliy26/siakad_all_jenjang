<?php

namespace App\Filament\Resources\ReferenceOptions;

use App\Filament\Resources\ReferenceOptions\Pages\CreateReferenceOption;
use App\Filament\Resources\ReferenceOptions\Pages\EditReferenceOption;
use App\Filament\Resources\ReferenceOptions\Pages\ListReferenceOptions;
use App\Filament\Resources\ReferenceOptions\Schemas\ReferenceOptionForm;
use App\Filament\Resources\ReferenceOptions\Tables\ReferenceOptionsTable;
use App\Models\ReferenceOption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReferenceOptionResource extends Resource
{
    protected static ?string $model = ReferenceOption::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return ReferenceOptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReferenceOptionsTable::configure($table);
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
            'index' => ListReferenceOptions::route('/'),
            'create' => CreateReferenceOption::route('/create'),
            'edit' => EditReferenceOption::route('/{record}/edit'),
        ];
    }
}
