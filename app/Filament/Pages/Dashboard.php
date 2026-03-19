<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Form;
use App\Models\TahunAkademik;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use App\Filament\Pages\DokumentasiAlurPage;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $form): Schema
    {
        if (!\Illuminate\Support\Facades\Auth::check() || !\Illuminate\Support\Facades\Auth::user()->hasRole('super_admin')) {
            return $form->schema([]);
        }

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('tahun_akademik')
                            ->label('Tahun Akademik')
                            ->options(TahunAkademik::all()->mapWithKeys(fn($t) => [$t->id => $t->nama . ' ' . $t->periode]))
                            ->default(fn() => TahunAkademik::latest('id')->value('id'))
                            ->searchable(),
                    ])
                    ->columns(1),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('dokumentasi_alur')
                ->label('Dokumentasi Alur SIAKAD')
                ->icon('heroicon-o-book-open')
                ->color('info')
                ->url(DokumentasiAlurPage::getUrl())
                ->openUrlInNewTab(),
        ];
    }
}
