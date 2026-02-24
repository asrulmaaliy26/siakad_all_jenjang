<?php

namespace App\Filament\Resources\Ulasans;

use App\Filament\Resources\Ulasans\Pages\CreateUlasan;
use App\Filament\Resources\Ulasans\Pages\EditUlasan;
use App\Filament\Resources\Ulasans\Pages\ListUlasans;
use App\Filament\Resources\Ulasans\Pages\ViewUlasan;
use App\Filament\Resources\Ulasans\Schemas\UlasanForm;
use App\Filament\Resources\Ulasans\Schemas\UlasanInfolist;
use App\Filament\Resources\Ulasans\Tables\UlasansTable;
use App\Models\Ulasan;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UlasanResource extends Resource
{
    protected static ?string $model = Ulasan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string|UnitEnum|null $navigationGroup = 'Feedback';
    protected static ?string $navigationLabel = 'Ulasan & Bintang';
    protected static ?int $navigationSort = 100;

    public static function form(Schema $schema): Schema
    {
        return UlasanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UlasanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UlasansTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
            $query->where('user_id', Auth::id());
        }

        return $query;
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
            'index' => ListUlasans::route('/'),
            'create' => CreateUlasan::route('/create'),
            'view' => ViewUlasan::route('/{record}'),
            'edit' => EditUlasan::route('/{record}/edit'),
        ];
    }
}
