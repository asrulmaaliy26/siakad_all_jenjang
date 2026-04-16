<?php

namespace App\Filament\Resources\MonitoringAbsensi\Pages;

use App\Filament\Resources\MonitoringAbsensi\MonitoringAbsensiResource;
use Filament\Resources\Pages\ListRecords;

class ListMonitoringAbsensis extends ListRecords
{
    protected static string $resource = MonitoringAbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
