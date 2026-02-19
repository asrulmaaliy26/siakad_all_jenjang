<?php

namespace App\Filament\Resources\SiswaData\Pages;

use App\Filament\Resources\SiswaData\SiswaDataResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListSiswaData extends ListRecords
{
    protected static string $resource = SiswaDataResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Siswa'),
            'aktif' => Tab::make('Siswa Aktif')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status_siswa', 'aktif')),
            'tidak aktif' => Tab::make('Siswa Tidak Aktif')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status_siswa', 'tidak aktif')->orWhereNull('status_siswa')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('download_arsip')
                ->label('Download Arsip')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn(): string => SiswaDataResource::getUrl('download-files'))
                ->disabled(fn() => !\Filament\Facades\Filament::auth()->user()?->hasAnyRole(['super_admin', 'admin'])),
        ];
    }
}
