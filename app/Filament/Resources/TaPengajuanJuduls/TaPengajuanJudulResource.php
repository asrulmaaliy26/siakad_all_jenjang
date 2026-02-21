<?php

namespace App\Filament\Resources\TaPengajuanJuduls;

use App\Filament\Resources\TaPengajuanJuduls\Pages\CreateTaPengajuanJudul;
use App\Filament\Resources\TaPengajuanJuduls\Pages\EditTaPengajuanJudul;
use App\Filament\Resources\TaPengajuanJuduls\Pages\ListTaPengajuanJuduls;
use App\Filament\Resources\TaPengajuanJuduls\Pages\ViewTaPengajuanJudul;
use App\Filament\Resources\TaPengajuanJuduls\Schemas\TaPengajuanJudulForm;
use App\Filament\Resources\TaPengajuanJuduls\Schemas\TaPengajuanJudulInfolist;
use App\Filament\Resources\TaPengajuanJuduls\Tables\TaPengajuanJudulsTable;
use App\Models\TaPengajuanJudul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TaPengajuanJudulResource extends Resource
{
    protected static ?string $model = TaPengajuanJudul::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Pengajuan Judul TA';

    protected static string|UnitEnum|null $navigationGroup = 'Tugas Akhir';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Schema $schema): Schema
    {
        return TaPengajuanJudulForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaPengajuanJudulInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaPengajuanJudulsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaPengajuanJuduls::route('/'),
            'create' => CreateTaPengajuanJudul::route('/create'),
            'view'   => ViewTaPengajuanJudul::route('/{record}'),
            'edit'   => EditTaPengajuanJudul::route('/{record}/edit'),
        ];
    }
}
