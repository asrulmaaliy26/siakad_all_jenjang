<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\SarprasSuratKeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SarprasSuratController extends Controller
{
    public function cetak($id)
    {
        $surat = SarprasSuratKeluar::with(['kategori', 'user'])->findOrFail($id);
        
        $data = [
            'surat' => $surat,
            'isi_surat' => $surat->isi_surat,
        ];

        $pdf = Pdf::loadView('sarpras.surat.cetak', $data);
        
        $filename = str_replace(['/', '\\'], '-', $surat->nomor_surat);
        return $pdf->stream('Surat-' . $filename . '.pdf');
    }
}
