<?php

namespace App\Filament\Resources\Akms;

use App\Filament\Resources\Akms\Pages\CreateAkm;
use App\Filament\Resources\Akms\Pages\EditAkm;
use App\Filament\Resources\Akms\Pages\ListAkms;
use App\Filament\Resources\Akms\Pages\ViewAkm;
use App\Filament\Resources\Akms\Schemas\AkmForm;
use App\Filament\Resources\Akms\Schemas\AkmInfolist;
use App\Filament\Resources\Akms\Tables\AkmsTable;
use App\Models\AkademikKrs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AkmResource extends Resource
{
    protected static ?string $model = AkademikKrs::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string | UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?string $navigationLabel = 'AKM (Aktivitas Kuliah)';
    protected static ?string $modelLabel = 'Aktivitas Kuliah Mahasiswa';
    protected static ?string $pluralModelLabel = 'Aktivitas Kuliah Mahasiswa';
    protected static ?int $navigationSort = 26;

    public static function form(Schema $schema): Schema
    {
        return AkmForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AkmInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkmsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\MataPelajaranKelas\RelationManagers\SiswaDataLjkRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAkms::route('/'),
            // 'create' => CreateAkm::route('/create'),
            'view' => ViewAkm::route('/{record}'),
            // 'edit' => EditAkm::route('/{record}/edit'),
        ];
    }
}
