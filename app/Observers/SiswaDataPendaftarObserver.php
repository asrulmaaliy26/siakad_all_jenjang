<?php

namespace App\Observers;

use App\Models\SiswaDataPendaftar;
use App\Services\StudentActivationService;
use Illuminate\Support\Facades\Log;

class SiswaDataPendaftarObserver
{
    /**
     * Handle the SiswaDataPendaftar "updated" event.
     */
    public function updated(SiswaDataPendaftar $pendaftar): void
    {
        // Cek apakah Status_Pendaftaran berubah menjadi 'Y' (Diterima / Aktif)
        if ($pendaftar->isDirty('Status_Pendaftaran') && $pendaftar->Status_Pendaftaran === 'Y') {
            $siswaData = $pendaftar->siswa;

            if (!$siswaData) {
                Log::error("SiswaData tidak ditemukan untuk pendaftar ID: {$pendaftar->id}");
                return;
            }

            app(StudentActivationService::class)->activateStudent($siswaData);
        }
    }
}
