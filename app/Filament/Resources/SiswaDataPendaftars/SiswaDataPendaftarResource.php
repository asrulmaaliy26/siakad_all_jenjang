<?php

namespace App\Filament\Resources\SiswaDataPendaftars;

use App\Filament\Resources\SiswaDataPendaftars\Pages\CreateSiswaDataPendaftar;
use App\Filament\Resources\SiswaDataPendaftars\Pages\EditSiswaDataPendaftar;
use App\Filament\Resources\SiswaDataPendaftars\Pages\ListSiswaDataPendaftars;
use App\Filament\Resources\SiswaDataPendaftars\Schemas\SiswaDataPendaftarForm;
use App\Filament\Resources\SiswaDataPendaftars\Tables\SiswaDataPendaftarsTable;
use App\Models\SiswaDataPendaftar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiswaDataPendaftarResource extends Resource
{
    protected static ?string $model = SiswaDataPendaftar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan User';
    protected static ?string $navigationLabel = 'Data Pendaftar';
    protected static ?string $modelLabel = 'Data Pendaftar';
    protected static ?string $pluralModelLabel = 'Data Pendaftar';
    protected static ?int $navigationSort = 32;



    public static function form(Schema $schema): Schema
    {
        return SiswaDataPendaftarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswaDataPendaftarsTable::configure($table);
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
            'index' => ListSiswaDataPendaftars::route('/'),
            'create' => CreateSiswaDataPendaftar::route('/create'),
            'edit' => EditSiswaDataPendaftar::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Jika user memiliki role 'murid' dan bukan super_admin/admin
        if ($user && $user->isMurid()) {
            $query->whereHas('siswa', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
