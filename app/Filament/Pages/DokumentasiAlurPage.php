<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use BackedEnum;
use UnitEnum;

class DokumentasiAlurPage extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Dokumentasi Alur';

    protected static string|UnitEnum|null $navigationGroup = 'Layanan Mahasiswa';

    protected static ?string $slug = 'dokumentasi-alur';

    protected static ?string $title = 'Dokumentasi Alur SIAKAD';

    protected string $view = 'filament.pages.dokumentasi-alur-page';

    public function mount(): void
    {
        // No longer needed: Markdown conversion
    }
}

