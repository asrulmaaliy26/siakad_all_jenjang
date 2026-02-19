<?php

namespace App\Filament\Resources\PengaturanPendaftarans\Pages;

use App\Filament\Resources\PengaturanPendaftarans\PengaturanPendaftaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePengaturanPendaftarans extends ManageRecords
{
    protected static string $resource = PengaturanPendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
