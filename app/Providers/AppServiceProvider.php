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
    }
}
