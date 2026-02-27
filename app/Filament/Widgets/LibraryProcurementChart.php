<?php

namespace App\Filament\Widgets;

use App\Models\LibraryProcurement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class LibraryProcurementChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('View:LibraryProcurementChart');
    }

    protected ?string $heading = 'Trend Pembelian Buku (6 Bulan)';
    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $data = LibraryProcurement::select(
            DB::raw("DATE_FORMAT(procurement_date, '%Y-%m') as month"),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('procurement_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            $row = $data->firstWhere('month', $month);
            $values[] = $row ? (float) $row->total : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembelian',
                    'data' => $values,
                    'backgroundColor' => '#f59e0b', // amber
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
