<?php

namespace App\Livewire;

use App\Models\JenjangPendidikan;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class GlobalJenjangSwitch extends Component
{
    public $activeJenjangId;

    public function mount()
    {
        $sessionJenjangId = Session::get('active_jenjang_id');

        // Validasi apakah ID di session masih valid di database
        if ($sessionJenjangId && JenjangPendidikan::find($sessionJenjangId)) {
            $this->activeJenjangId = $sessionJenjangId;
        } else {
            // Jika tidak ada atau tidak valid, ambil default
            $default = JenjangPendidikan::first();
            if ($default) {
                $this->activeJenjangId = $default->id;
                Session::put('active_jenjang_id', $default->id);
            }
        }
    }

    public function updatedActiveJenjangId($value)
    {
        Session::put('active_jenjang_id', $value);
        // Refresh halaman agar global scope / filter diterapkan
        return redirect(url()->previous());
    }

    public function render()
    {
        return view('livewire.global-jenjang-switch', [
            'jenjangs' => JenjangPendidikan::orderByRaw("CASE WHEN nama = 'UMUM' OR type = 'UMUM' THEN 0 ELSE 1 END")->get(),
        ]);
    }
}
