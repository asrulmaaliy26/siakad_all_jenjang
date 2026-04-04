<?php

namespace App\Filament\Resources\SarprasBarangs\Pages;

use App\Filament\Resources\SarprasBarangs\SarprasBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSarprasBarangs extends ListRecords
{
    protected static string $resource = SarprasBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Baik' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kondisi', 'Baik')),
            'Rusak Ringan' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kondisi', 'Rusak Ringan')),
            'Rusak Berat' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kondisi', 'Rusak Berat')),
            'Semua' => Tab::make(),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'Baik';
    }
}
