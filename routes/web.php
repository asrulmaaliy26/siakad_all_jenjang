<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'index'])->name('pendaftaran.index');
Route::post('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'store'])->name('pendaftaran.store');

Route::get('/cetak-absensi-kosong/{id_mata_pelajaran_kelas}', function ($id) {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect('/');
    }

    $kelas = \App\Models\MataPelajaranKelas::with(['dosenData', 'mataPelajaranKurikulum.mataPelajaranMaster', 'kelas.tahunAkademik', 'kelas.programKelas', 'ruangKelas'])->findOrFail($id);
    $krsList = \App\Models\AkademikKRS::where('id_kelas', $kelas->id_kelas)
        ->with('riwayatPendidikan.siswaData')
        ->get()
        ->sortBy(function ($krs) {
            return $krs->riwayatPendidikan->siswaData->nama ?? '';
        });

    return view('cetak.absensi-kosong', compact('kelas', 'krsList'));
})->name('cetak.absensi.kosong');

Route::get('/cetak-absensi-terisi/{id_mata_pelajaran_kelas}', function ($id) {
    if (!\Illuminate\Support\Facades\Auth::check()) {
        return redirect('/');
    }

    $kelas = \App\Models\MataPelajaranKelas::with(['dosenData', 'mataPelajaranKurikulum.mataPelajaranMaster', 'kelas.tahunAkademik', 'kelas.programKelas', 'ruangKelas'])->findOrFail($id);
    $krsList = \App\Models\AkademikKRS::where('id_kelas', $kelas->id_kelas)
        ->with('riwayatPendidikan.siswaData')
        ->get()
        ->sortBy(function ($krs) {
            return $krs->riwayatPendidikan->siswaData->nama ?? '';
        });

    $sesiAbsensi = \App\Models\AbsensiSiswa::where('id_mata_pelajaran_kelas', $id)
        ->selectRaw('DATE(waktu_absen) as tanggal')
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->get();

    $absensiData = \App\Models\AbsensiSiswa::where('id_mata_pelajaran_kelas', $id)->get();

    return view('cetak.absensi-terisi', compact('kelas', 'krsList', 'sesiAbsensi', 'absensiData'));
})->name('cetak.absensi.terisi');

// ── Cetak KRS PDF ─────────────────────────────────────────────────────────
Route::get('/cetak-krs/{id}', function ($id) {
    if (! \Illuminate\Support\Facades\Auth::check()) {
        return redirect('/');
    }

    $krs = \App\Models\AkademikKrs::with([
        'riwayatPendidikan.siswa',
        'riwayatPendidikan.jurusan.jenjangPendidikan',
        'kelas.tahunAkademik',
        'kelas.programKelas',
        'siswaDataLjk.mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster',
        'siswaDataLjk.mataPelajaranKelas.dosenData',
    ])->findOrFail($id);

    // Proteksi: murid hanya bisa cetak KRS miliknya sendiri
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->isMurid()) {
        $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
        if (! $siswa || $krs->riwayatPendidikan?->id_siswa_data !== $siswa->id) {
            abort(403, 'Anda tidak berhak mencetak KRS ini.');
        }
    }

    // Hitung total SKS berdasarkan LJK yang diambil
    $totalSksLjk = $krs->siswaDataLjk->sum(function ($ljk) {
        return $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
    });

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cetak.krs', compact('krs', 'totalSksLjk'))
        ->setPaper('a4', 'portrait');

    $namaSiswa = \Illuminate\Support\Str::slug($krs->riwayatPendidikan?->siswa?->nama ?? 'krs');
    $semester  = $krs->semester ?? 'x';

    return $pdf->stream("KRS-{$namaSiswa}-smt{$semester}.pdf");
})->name('cetak.krs');
