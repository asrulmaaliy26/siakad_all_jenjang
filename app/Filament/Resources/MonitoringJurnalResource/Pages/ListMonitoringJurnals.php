<?php

namespace App\Filament\Resources\MonitoringJurnalResource\Pages;

use App\Filament\Resources\MonitoringJurnalResource\MonitoringJurnalResource;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMonitoringJurnals extends ListRecords
{
    protected static string $resource = MonitoringJurnalResource::class;

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
