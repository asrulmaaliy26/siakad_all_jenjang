<?php

namespace App\Filament\Resources\PklPendaftarans\Pages;

use App\Filament\Resources\PklPendaftarans\PklPendaftaranResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\PklPeriode;

class CreatePklPendaftaran extends CreateRecord
{
    protected static string $resource = PklPendaftaranResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->data;
        $periodeId = $data['id_pkl_periode'];
        $lembagaId = $data['id_pkl_lembaga'];
        $siswaId = $data['id_siswa_data'];

        // Cek apakah mahasiswa sudah terdaftar di periode ini
        $existing = \App\Models\PklPendaftaran::where('id_pkl_periode', $periodeId)
            ->where('id_siswa_data', $siswaId)
            ->first();
        
        if ($existing) {
            Notification::make()
                ->title('Sudah Terdaftar')
                ->body('Mahasiswa sudah memiliki pendaftaran di periode PKL ini.')
                ->danger()
                ->send();

            $this->halt();
        }

        $periode = PklPeriode::find($periodeId);
        $lembaga = $periode->lembagas()->where('pkl_lembagas.id', $lembagaId)->first();

        if ($lembaga) {
            $terisi = $lembaga->pendaftarans()->where('id_pkl_periode', $periodeId)->count();
            $kuota = $lembaga->pivot->kuota ?? 0;

            if ($terisi >= $kuota) {
                Notification::make()
                    ->title('Kuota Penuh')
                    ->body('Maaf, kuota untuk lembaga ini sudah penuh.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }
    }
}
