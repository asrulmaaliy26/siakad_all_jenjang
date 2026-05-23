<?php

namespace App\Filament\Resources\PklPendaftarans;

use App\Filament\Resources\PklPendaftarans\Pages\CreatePklPendaftaran;
use App\Filament\Resources\PklPendaftarans\Pages\EditPklPendaftaran;
use App\Filament\Resources\PklPendaftarans\Pages\ListPklPendaftarans;
use App\Filament\Resources\PklPendaftarans\Pages\ViewPklPendaftaran;
use App\Filament\Resources\PklPendaftarans\Schemas\PklPendaftaranForm;
use App\Filament\Resources\PklPendaftarans\Tables\PklPendaftaransTable;
use App\Models\PklPendaftaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PklPendaftaranResource extends Resource
{
    protected static ?string $model = PklPendaftaran::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Pendaftaran PKL';

    protected static string|UnitEnum|null $navigationGroup = 'PKL';

    protected static ?int $navigationSort = 52;

    public static function form(Schema $schema): Schema
    {
        return PklPendaftaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PklPendaftaransTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->isMurid()) {
            $siswaId = $user->getSiswaId();
            $query->where('id_siswa_data', $siswaId);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPklPendaftarans::route('/'),
            'create' => CreatePklPendaftaran::route('/create'),
            'view'   => ViewPklPendaftaran::route('/{record}'),
            'edit'   => EditPklPendaftaran::route('/{record}/edit'),
        ];
    }
}
