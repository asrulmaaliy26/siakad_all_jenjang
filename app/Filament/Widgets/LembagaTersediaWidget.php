<?php

namespace App\Filament\Widgets;

use App\Models\PklPeriode;
use App\Models\PklLembaga;
use App\Models\PklPendaftaran;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class LembagaTersediaWidget extends Widget
{
    protected string $view = 'filament.widgets.lembaga-tersedia-widget';

    protected int | string | array $columnSpan = 'full';

    public ?PklPeriode $activePeriode = null;
    public array $lembagas = [];
    public bool $hasRegistered = false;

    public function mount(): void
    {
        $user = Auth::user();
        
        // Cek apakah sudah mendaftar
        if ($user->isMurid()) {
            $siswaId = $user->getSiswaId();
            $this->hasRegistered = PklPendaftaran::where('id_siswa_data', $siswaId)->exists();
        }

        // Cari periode aktif
        $this->activePeriode = PklPeriode::where('is_active', true)
            ->where('tgl_mulai', '<=', now())
            ->where('tgl_selesai', '>=', now())
            ->first();

        if ($this->activePeriode) {
            $this->lembagas = $this->activePeriode->lembagas->map(function ($lembaga) {
                $terisi = PklPendaftaran::where('id_pkl_periode', $this->activePeriode->id)
                    ->where('id_pkl_lembaga', $lembaga->id)
                    ->count();
                
                return [
                    'id' => $lembaga->id,
                    'nama' => $lembaga->nama,
                    'kuota' => $lembaga->pivot->kuota,
                    'sisa' => $lembaga->pivot->kuota - $terisi,
                    'website' => $lembaga->website,
                ];
            })->toArray();
        }
    }

    public function daftar(int $lembagaId): void
    {
        $user = Auth::user();
        if (!$user->isMurid()) return;

        $siswaId = $user->getSiswaId();
        if (!$siswaId) return;

        // Validasi periode
        if (!$this->activePeriode) {
            \Filament\Notifications\Notification::make()
                ->title('Gagal mendaftar')
                ->body('Periode PKL tidak aktif.')
                ->danger()
                ->send();
            return;
        }

        // Cek duplikasi
        if (PklPendaftaran::where('id_siswa_data', $siswaId)->exists()) {
            \Filament\Notifications\Notification::make()
                ->title('Sudah terdaftar')
                ->body('Anda sudah melakukan pendaftaran PKL.')
                ->warning()
                ->send();
            return;
        }

        // Cek kuota
        $lembaga = $this->activePeriode->lembagas()->where('pkl_lembagas.id', $lembagaId)->first();
        if (!$lembaga) return;

        $terisi = PklPendaftaran::where('id_pkl_periode', $this->activePeriode->id)
            ->where('id_pkl_lembaga', $lembagaId)
            ->count();
        
        $kuota = $lembaga->pivot->kuota ?? 0;

        if ($terisi >= $kuota) {
            \Filament\Notifications\Notification::make()
                ->title('Kuota penuh')
                ->body('Maaf, kuota untuk lembaga ini sudah habis.')
                ->danger()
                ->send();
            return;
        }

        // Simpan pendaftaran
        PklPendaftaran::create([
            'id_pkl_periode' => $this->activePeriode->id,
            'id_pkl_lembaga' => $lembagaId,
            'id_siswa_data' => $siswaId,
            'tgl_daftar' => now(),
            'status' => 'pending',
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Berhasil mendaftar')
            ->body('Pendaftaran Anda di ' . $lembaga->nama . ' telah berhasil.')
            ->success()
            ->send();

        $this->mount(); // Refresh data widget
        $this->dispatch('refreshTable'); // Jika ada tabel pendaftaran di bawahnya
    }

    public static function canView(): bool
    {
        return Auth::user()->isMurid();
    }
}
