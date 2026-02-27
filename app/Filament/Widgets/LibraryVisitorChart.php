<?php

namespace App\Filament\Widgets;

use App\Models\LibraryVisit;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;


class LibraryVisitorChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('View:LibraryVisitorChart');
    }

    protected ?string $heading = 'Statistik Pengunjung (7 Hari Terakhir)';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = LibraryVisit::select(DB::raw('DATE(visited_at) as date'), DB::raw('count(*) as aggregate'))
            ->where('visited_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $row = $data->firstWhere('date', $date);
            $values[] = $row ? $row->aggregate : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengunjung',
                    'data' => $values,
                    'fill' => 'start',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
