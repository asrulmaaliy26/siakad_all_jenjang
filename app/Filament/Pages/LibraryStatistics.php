<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LibraryLoanChart;
use App\Filament\Widgets\LibraryOverviewStats;
use App\Filament\Widgets\LibraryVisitorChart;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;

class LibraryStatistics extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Statistik Perpustakaan';
    protected static ?string $title = 'Statistik Perpustakaan';
    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.library-statistics';

    protected function getHeaderWidgets(): array
    {
        return [
            LibraryOverviewStats::class,
        ];
    }
}
