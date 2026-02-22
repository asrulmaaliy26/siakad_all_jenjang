<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer untuk auto-create RiwayatPendidikan saat status kelulusan = Lulus
        \App\Models\SiswaDataPendaftar::observe(\App\Observers\SiswaDataPendaftarObserver::class);
        \App\Models\JenjangPendidikan::observe(\App\Observers\JenjangObserver::class);

        // -- Global Configuration untuk mempercantik Filament --

        // Mempercantik Table secara Global
        \Filament\Tables\Table::configureUsing(function (\Filament\Tables\Table $table): void {
            $table
                ->emptyStateHeading('Tidak ada data')
                ->emptyStateDescription('Belum ada record data yang tersedia di tabel ini.')
                ->emptyStateIcon('heroicon-o-folder-open')
                ->striped() // Membuat tabel menjadi bergaris (belang-belang) agar lebih rapi
                ->defaultPaginationPageOption(10) // Pagination default 10 baris
                ->extremePaginationLinks() // Menggunakan link pagination prev-next & first-last
                ->paginated([10, 25, 50, 100]); // Opsi pilihan pagination
        });

        // Mempercantik form input secara default (Global Form Config)
        \Filament\Forms\Components\Select::configureUsing(function (\Filament\Forms\Components\Select $select): void {
            $select->searchable()->preload(); // Semua select jadi searchable & loading preload
        });

        \Filament\Forms\Components\DatePicker::configureUsing(function (\Filament\Forms\Components\DatePicker $date): void {
            $date->displayFormat('d M Y'); // Format tanggal lebih enak dibaca (contoh: 01 Jan 2025)
        });
    }
}
