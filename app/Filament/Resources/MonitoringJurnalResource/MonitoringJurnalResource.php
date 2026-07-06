<?php

namespace App\Filament\Resources\MonitoringJurnalResource;

use App\Filament\Resources\MonitoringJurnalResource\Pages\ListMonitoringJurnals;
use App\Filament\Resources\MonitoringJurnalResource\Pages\ViewMonitoringJurnal;
use App\Filament\Resources\MonitoringJurnalResource\Tables\MonitoringJurnalTable;
use App\Models\MataPelajaranKelas;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class MonitoringJurnalResource extends Resource
{
    protected static ?string $model = MataPelajaranKelas::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin', 'admin_jenjang_s1', 'admin_jenjang_ma', 'admin_jenjang_smp']);
    }

    public static function getNavigationLabel(): string
    {
        return 'Monitoring Jurnal';
    }

    public static function getModelLabel(): string
    {
        return 'Monitoring Jurnal';
    }

    public static function table(Table $table): Table
    {
        return MonitoringJurnalTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\MataPelajaranKelas\RelationManagers\JurnalPengajaranRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMonitoringJurnals::route('/'),
            'view' => ViewMonitoringJurnal::route('/{record}'),
        ];
    }
}
