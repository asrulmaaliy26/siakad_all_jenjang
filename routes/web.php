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
