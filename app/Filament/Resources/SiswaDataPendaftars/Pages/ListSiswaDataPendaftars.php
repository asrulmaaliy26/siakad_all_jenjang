<?php

namespace App\Filament\Resources\SiswaDataPendaftars\Pages;

use App\Filament\Resources\SiswaDataPendaftars\SiswaDataPendaftarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListSiswaDataPendaftars extends ListRecords
{
    protected static string $resource = SiswaDataPendaftarResource::class;

    public function mount(): void
    {
        parent::mount();

        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && ($user->isPendaftar() || $user->isMurid()) && !$user->isAdmin()) {
            $pendaftar = \App\Models\SiswaDataPendaftar::where('id_siswa_data', $user->siswaData?->id)->first();
            if ($pendaftar) {
                redirect()->to(SiswaDataPendaftarResource::getUrl('edit', ['record' => $pendaftar]));
            } else {
                redirect()->to(SiswaDataPendaftarResource::getUrl('create'));
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pendaftar'),

            'belum_validasi' => Tab::make('Belum Divalidasi')
                ->modifyQueryUsing(fn($query) => $query->where('status_valid', '0'))
                ->badge(fn() => \App\Models\SiswaDataPendaftar::where('status_valid', '0')->count()),

            'sudah_validasi' => Tab::make('Sudah Divalidasi')
                ->modifyQueryUsing(fn($query) => $query->where('status_valid', '1'))
                ->badge(fn() => \App\Models\SiswaDataPendaftar::where('status_valid', '1')->count()),

            'proses' => Tab::make('Proses')
                ->modifyQueryUsing(fn($query) => $query->where('Status_Pendaftaran', 'B'))
                ->badge(fn() => \App\Models\SiswaDataPendaftar::where('Status_Pendaftaran', 'B')->count())
                ->badgeColor('warning'),

            'lulus' => Tab::make('Lulus')
                ->modifyQueryUsing(fn($query) => $query->where('Status_Pendaftaran', 'Y'))
                ->badge(fn() => \App\Models\SiswaDataPendaftar::where('Status_Pendaftaran', 'Y')->count())
                ->badgeColor('success'),

            'tidak_lulus' => Tab::make('Tidak Lulus')
                ->modifyQueryUsing(fn($query) => $query->where('Status_Pendaftaran', 'N'))
                ->badge(fn() => \App\Models\SiswaDataPendaftar::where('Status_Pendaftaran', 'N')->count())
                ->badgeColor('danger'),
        ];
    }
}
