<?php

namespace App\Filament\Resources\PengajuanSurats\Pages;

use App\Filament\Resources\PengajuanSurats\PengajuanSuratResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePengajuanSurats extends ManageRecords
{
    protected static string $resource = PengajuanSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    /** @var \App\Models\User $user */
                    $user = \Filament\Facades\Filament::auth()->user();
                    if ($user && $user->isMurid()) {
                        $riwayat = \App\Models\RiwayatPendidikan::whereHas('siswa', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })->where('status', 'Aktif')->first();

                        if ($riwayat) {
                            $data['id_riwayat_pendidikan'] = $riwayat->id;
                        }

                        $tahunAktif = \App\Models\TahunAkademik::where('status', 'Y')->first();
                        if ($tahunAktif) {
                            $data['id_tahun_akademik'] = $tahunAktif->id;
                        }
                    }
                    return $data;
                }),
        ];
    }
}
