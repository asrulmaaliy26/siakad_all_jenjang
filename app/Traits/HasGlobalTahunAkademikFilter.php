<?php

namespace App\Traits;

use App\Models\TahunAkademik;
use Filament\Tables\Filters\SelectFilter;

trait HasGlobalTahunAkademikFilter
{
    /**
     * Get a standardized Global Tahun Akademik Filter for Filament Tables.
     *
     * @param string $columnName Default column name for the filter
     * @param bool $isMultiple Whether to allow multiple selections
     * @param callable|null $queryCallback Custom query callback
     * @return SelectFilter
     */
    public static function getGlobalTahunAkademikFilter(string $columnName = 'id_tahun_akademik', bool $isMultiple = false, callable $queryCallback = null): SelectFilter
    {
        $filter = SelectFilter::make($columnName)
            ->label('Tahun Akademik')
            ->options(fn() => TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
            ->searchable()
            ->preload()
            ->native(false);

        if ($isMultiple) {
            $filter->multiple();
        } else {
            $filter->default(fn () => session('global_tahun_akademik_id') ?? TahunAkademik::where('status', 'Y')->latest()->first()?->id);
        }

        if ($queryCallback) {
            $filter->query($queryCallback);
        }

        return $filter;
    }
}
