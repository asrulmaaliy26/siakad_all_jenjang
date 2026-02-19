<?php

namespace App\Filament\Resources\Kurikulums;

use App\Filament\Resources\Kurikulums\RelationManagers\MataPelajaranKurikulumRelationManager;
use App\Filament\Resources\Kurikulums\Pages\CreateKurikulum;
use App\Filament\Resources\Kurikulums\Pages\EditKurikulum;
use App\Filament\Resources\Kurikulums\Pages\ListKurikulums;
use App\Filament\Resources\Kurikulums\Pages\ViewKurikulum;
use App\Filament\Resources\Kurikulums\Schemas\KurikulumForm;
use App\Filament\Resources\Kurikulums\Tables\KurikulumsTable;
use App\Filament\Resources\Kurikulums\RelationManagers\KurikulumRelationManager;
use App\Models\Kurikulum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KurikulumResource extends Resource
{
    protected static ?string $model = Kurikulum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Kurikulum ✓';

    protected static ?int $navigationSort = 13;

    public static function form(Schema $schema): Schema
    {
        return KurikulumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KurikulumsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            // KurikulumRelationManager::class,
            MataPelajaranKurikulumRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKurikulums::route('/'),
            'create' => CreateKurikulum::route('/create'),
            'view' => ViewKurikulum::route('/{record}'),
            'edit' => EditKurikulum::route('/{record}/edit'),
        ];
    }
}
