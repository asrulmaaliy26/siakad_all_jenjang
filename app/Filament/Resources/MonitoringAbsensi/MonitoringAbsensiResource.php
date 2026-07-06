<?php

namespace App\Filament\Resources\MonitoringAbsensi;

use App\Filament\Resources\MonitoringAbsensi\Pages\ListMonitoringAbsensis;
use App\Filament\Resources\MonitoringAbsensi\Pages\ViewMonitoringAbsensi;
use App\Filament\Resources\MonitoringAbsensi\Tables\MonitoringAbsensiTable;
use App\Models\MataPelajaranKelas;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class MonitoringAbsensiResource extends Resource
{
    protected static ?string $model = MataPelajaranKelas::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

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
        return 'Monitoring Absensi';
    }

    public static function getModelLabel(): string
    {
        return 'Monitoring Absensi';
    }

    public static function table(Table $table): Table
    {
        return MonitoringAbsensiTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\MataPelajaranKelas\RelationManagers\AbsensiSiswaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMonitoringAbsensis::route('/'),
            'view' => ViewMonitoringAbsensi::route('/{record}'),
        ];
    }
}
