<?php

namespace App\Filament\Widgets;

use App\Models\LibraryLoan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class LibraryLoanChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('View:LibraryLoanChart');
    }

    protected ?string $heading = 'Status Peminjaman';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statusCounts = LibraryLoan::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [
            'borrowed' => 'Dipinjam',
            'returned' => 'Kembali',
            'overdue' => 'Terlambat',
            'lost' => 'Hilang',
        ];

        $data = [];
        $finalLabels = [];
        $colors = [
            'borrowed' => '#3b82f6', // blue
            'returned' => '#22c55e', // green
            'overdue' => '#ef4444',  // red
            'lost' => '#6b7280',     // gray
        ];
        $backgroundColors = [];

        foreach ($labels as $key => $label) {
            $count = $statusCounts[$key] ?? 0;
            if ($count > 0 || count($statusCounts) == 0) {
                $data[] = $count;
                $finalLabels[] = $label;
                $backgroundColors[] = $colors[$key];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Status Peminjaman',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $finalLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
