<?php

namespace App\Filament\Resources\MonitoringAbsensi\Pages;

use App\Filament\Resources\MonitoringAbsensi\MonitoringAbsensiResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoringAbsensi extends ViewRecord
{
    protected static string $resource = MonitoringAbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
