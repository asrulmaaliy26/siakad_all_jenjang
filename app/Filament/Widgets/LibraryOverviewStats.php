<?php

namespace App\Filament\Widgets;

use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LibraryVisit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class LibraryOverviewStats extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('View:LibraryOverviewStats');
    }

    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Buku', LibraryBook::sum('stock'))
                ->description('Jumlah buku tersedia')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),
            Stat::make('Kunjungan Hari Ini', LibraryVisit::whereDate('visited_at', now())->count())
                ->description('Pengunjung hari ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('Peminjaman Aktif', LibraryLoan::where('status', 'borrowed')->count())
                ->description('Buku yang sedang dipinjam')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('warning'),
        ];
    }
}
