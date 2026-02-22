<?php

namespace App\Filament\Resources\SiswaDataLJKS;

use App\Filament\Resources\SiswaDataLJKS\Pages\CreateSiswaDataLJK;
use App\Filament\Resources\SiswaDataLJKS\Pages\EditSiswaDataLJK;
use App\Filament\Resources\SiswaDataLJKS\Pages\ListSiswaDataLJKS;
use App\Filament\Resources\SiswaDataLJKS\Schemas\SiswaDataLJKForm;
use App\Filament\Resources\SiswaDataLJKS\Tables\SiswaDataLJKSTable;
use App\Models\SiswaDataLJK;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiswaDataLJKResource extends Resource
{
    protected static ?string $model = SiswaDataLJK::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static string | UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?int $navigationSort = 45;
    protected static ?string $navigationLabel = 'Nilai';

    public static function form(Schema $schema): Schema
    {
        return SiswaDataLJKForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswaDataLJKSTable::configure($table);
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
            'index' => ListSiswaDataLJKS::route('/'),
            'create' => CreateSiswaDataLJK::route('/create'),
            'edit' => EditSiswaDataLJK::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Jika user memiliki role 'pengajar' dan bukan super_admin/admin
        if ($user && $user->isPengajar()) {
            $query->whereHas('mataPelajaranKelas', function ($q) use ($user) {
                $q->whereHas('dosenData', function ($dq) use ($user) {
                    $dq->where('user_id', $user->id);
                });
            });
        }

        // Jika user memiliki role 'murid' dan bukan super_admin/admin
        if ($user && $user->isMurid()) {
            $query->whereHas('akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
