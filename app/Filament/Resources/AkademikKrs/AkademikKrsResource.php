<?php

namespace App\Filament\Resources\AkademikKrs;

use App\Filament\Resources\AkademikKrs\Pages\CreateAkademikKrs;
use App\Filament\Resources\AkademikKrs\Pages\EditAkademikKrs;
use App\Filament\Resources\AkademikKrs\Pages\ListAkademikKrs;
use App\Filament\Resources\AkademikKrs\Pages\ViewAkademikKrs;
use App\Filament\Resources\AkademikKrs\Schemas\AkademikKrsForm;
use App\Filament\Resources\AkademikKrs\Tables\AkademikKrsTable;
use App\Models\AkademikKrs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AkademikKrsResource extends Resource
{
    protected static ?string $model = AkademikKrs::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static string | UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?int $navigationSort = 23;
    protected static ?string $navigationLabel = 'KRS';

    public static function form(Schema $schema): Schema
    {
        return AkademikKrsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkademikKrsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SiswaDataLjkRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAkademikKrs::route('/'),
            'create' => CreateAkademikKrs::route('/create'),
            'edit' => EditAkademikKrs::route('/{record}/edit'),
            'view' => ViewAkademikKrs::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user  = \Filament\Facades\Filament::auth()->user();

        // Murid hanya melihat KRS milik dirinya
        if ($user && $user->isMurid()) {
            $query->whereHas('riwayatPendidikan.siswaData', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Pengajar hanya melihat KRS dari mahasiswa bimbingannya (wali dosen)
        if ($user && $user->isPengajar()) {
            $dosenId = $user->getDosenId();
            if ($dosenId) {
                $query->whereHas('riwayatPendidikan', function ($q) use ($dosenId) {
                    $q->where('id_wali_dosen', $dosenId);
                });
            } else {
                // Jika tidak ada data dosen, jangan tampilkan apa-apa atau batasi
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }
}
