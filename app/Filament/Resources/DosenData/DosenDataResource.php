<?php

namespace App\Filament\Resources\DosenData;

use App\Filament\Resources\DosenData\Pages\CreateDosenData;
use App\Filament\Resources\DosenData\Pages\EditDosenData;
use App\Filament\Resources\DosenData\Pages\ListDosenData;
use App\Filament\Resources\DosenData\Pages\ViewDosenData;
use App\Filament\Resources\DosenData\Schemas\DosenDataForm;
use App\Filament\Resources\DosenData\Tables\DosenDataTable;
use App\Models\DosenData;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DosenDataResource extends Resource
{
    protected static ?string $model = DosenData::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 15;

    protected static ?string $recordTitleAttribute = 'nama';
    // protected static ?string $navigationLabel = 'Guru ';

    public static function getNavigationLabel(): string
    {
        return \App\Helpers\SiakadTerm::pengajar() . '';
    }

    public static function getModelLabel(): string
    {
        return \App\Helpers\SiakadTerm::pengajar();
    }

    public static function form(Schema $schema): Schema
    {
        return DosenDataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DosenDataTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\DosenData\RelationManagers\DosenDokumenRelationManager::class,
            \App\Filament\Resources\DosenData\RelationManagers\DosenRiwayatPendidikanDosenRelationManager::class,
            \App\Filament\Resources\DosenData\RelationManagers\DosenBukuRelationManager::class,
            \App\Filament\Resources\DosenData\RelationManagers\DosenPenelitianRelationManager::class,
            \App\Filament\Resources\DosenData\RelationManagers\DosenPengabdianRelationManager::class,
            \App\Filament\Resources\DosenData\RelationManagers\DosenPenghargaanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDosenData::route('/'),
            'create' => CreateDosenData::route('/create'),
            'view' => ViewDosenData::route('/{record}'),
            'edit' => EditDosenData::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Jika user memiliki role 'pengajar' dan bukan super_admin/admin
        if ($user && $user->isPengajar()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }
}
