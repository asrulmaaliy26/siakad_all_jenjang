<?php

namespace App\Filament\Resources\PklPendaftarans\Pages;

use App\Filament\Resources\PklPendaftarans\PklPendaftaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPklPendaftarans extends ListRecords
{
    protected static string $resource = PklPendaftaranResource::class;

    protected $listeners = ['refreshTable' => '$refresh'];

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\LembagaTersediaWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make()
            //     ->label(fn() => Auth::user()->isMurid() ? 'Daftar PKL' : 'Tambah Pendaftaran'),
        ];
    }
}
