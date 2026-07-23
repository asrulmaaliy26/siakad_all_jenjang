<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TahunAkademik;

class GlobalTahunAkademikSwitcher extends Component
{
    public $tahunAkademikId;
    public $tahunAkademiks;

    public function mount()
    {
        $this->tahunAkademiks = TahunAkademik::orderByDesc('id')->get();
        
        $this->tahunAkademikId = session('global_tahun_akademik_id');

        if (!$this->tahunAkademikId) {
            // Coba ambil yang statusnya Y jika belum ada di session
            $activeTa = TahunAkademik::where('status', 'Y')->latest()->first();
            if ($activeTa) {
                $this->tahunAkademikId = $activeTa->id;
                session(['global_tahun_akademik_id' => $this->tahunAkademikId]);
            }
        }
    }

    public function updatedTahunAkademikId($value)
    {
        session(['global_tahun_akademik_id' => (int) $value]);
        
        // Filament menggunakan session dengan format dot-notation 'tables.{md5}_filters',
        // yang oleh Laravel disimpan sebagai array multidimensi dengan root key 'tables'.
        // Dengan menghapus key 'tables', kita mereset memori semua tabel (termasuk filter, search, & sort)
        // sehingga tabel akan terpaksa memuat nilai default terbaru (dari global_tahun_akademik_id).
        session()->forget('tables');
        
        // Memicu event javascript untuk me-reload halaman tanpa query string
        $this->dispatch('reload-page');
    }

    public function render()
    {
        return view('livewire.global-tahun-akademik-switcher');
    }
}
