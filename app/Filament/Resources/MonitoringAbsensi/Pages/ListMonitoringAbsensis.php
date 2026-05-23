<?php

namespace App\Filament\Resources\MonitoringAbsensi\Pages;

use App\Filament\Resources\MonitoringAbsensi\MonitoringAbsensiResource;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMonitoringAbsensis extends ListRecords
{
    protected static string $resource = MonitoringAbsensiResource::class;

    public function getTabs(): array
    {
        $today = now()->locale('id')->dayName;

        return [
            'this_week' => Tab::make('Minggu Ini')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('hari')),
            'today' => Tab::make('Hari Ini (' . $today . ')')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('hari', $today)),
            'all' => Tab::make('Semua Mata Kuliah'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
