<?php

namespace App\Filament\Resources\Nilais;

use App\Filament\Resources\Nilais\Pages\CreateNilai;
use App\Filament\Resources\Nilais\Pages\EditNilai;
use App\Filament\Resources\Nilais\Pages\ListNilais;
use App\Filament\Resources\Nilais\Pages\ViewNilai;
use App\Filament\Resources\Nilais\Schemas\NilaiForm;
use App\Filament\Resources\Nilais\Schemas\NilaiInfolist;
use App\Filament\Resources\Nilais\Tables\NilaisTable;
use App\Models\MataPelajaranKelas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NilaiResource extends Resource
{
    protected static ?string $model = MataPelajaranKelas::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static string | UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?int $navigationSort = 25;
    protected static ?string $navigationLabel = 'Nilai';
    protected static ?string $modelLabel = 'Nilai Kelas';
    protected static ?string $pluralModelLabel = 'Nilai Kelas';

    public static function form(Schema $schema): Schema
    {
        return NilaiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return NilaiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NilaisTable::configure($table);
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
            'index' => ListNilais::route('/'),
            'view' => ViewNilai::route('/{record}'),
        ];
    }
}
