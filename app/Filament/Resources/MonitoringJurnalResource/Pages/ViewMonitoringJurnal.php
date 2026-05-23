<?php

namespace App\Filament\Resources\MonitoringJurnalResource\Pages;

use App\Filament\Resources\MonitoringJurnalResource\MonitoringJurnalResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoringJurnal extends ViewRecord
{
    protected static string $resource = MonitoringJurnalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
