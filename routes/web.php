<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
use App\Http\Controllers\CetakController;

Route::get('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'index'])->name('pendaftaran.index');
Route::post('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'store'])->name('pendaftaran.store');
Route::get('/referal-qr/{kode}/download', [App\Http\Controllers\PendaftaranController::class, 'downloadQr'])->name('referal.qr.download');

Route::middleware('auth')->group(function () {
    Route::get('/cetak-absensi-kosong/{id_mata_pelajaran_kelas}', [CetakController::class, 'absensiKosong'])->name('cetak.absensi.kosong');
    Route::get('/cetak-absensi-terisi/{id_mata_pelajaran_kelas}', [CetakController::class, 'absensiTerisi'])->name('cetak.absensi.terisi');
    Route::get('/cetak-krs/{id}', [CetakController::class, 'krs'])->name('cetak.krs');
    Route::get('/cetak-pengajuan-surat/{id}', [CetakController::class, 'pengajuanSurat'])->name('cetak.pengajuan.surat');
    Route::get('/cetak-kartu-bimbingan-sempro/{id}', [CetakController::class, 'kartuBimbinganSempro'])->name('cetak.kartu-bimbingan.sempro');
    Route::get('/cetak-kartu-bimbingan-judul/{id}', [CetakController::class, 'kartuBimbinganJudul'])->name('cetak.kartu-bimbingan.judul');
    Route::get('/cetak-kartu-bimbingan-skripsi/{id}', [CetakController::class, 'kartuBimbinganSkripsi'])->name('cetak.kartu-bimbingan.skripsi');
    Route::get('/cetak-khs/{id}', [CetakController::class, 'khs'])->name('cetak.khs');
    Route::get('/cetak-transkrip/{id}', [CetakController::class, 'transkrip'])->name('cetak.transkrip');
    Route::get('/cetak-ktm/{id}', [CetakController::class, 'ktm'])->name('cetak.ktm');
    
    // Sarpras Correspondence
    Route::get('/sarpras/surat-keluar/{id}/cetak', [App\Http\Controllers\Sarpras\SarprasSuratController::class, 'cetak'])->name('sarpras.surat-keluar.cetak');

    // Ujian Fullscreen
    Route::get('/ujian/{mpkId}/{type}', [App\Http\Controllers\UjianFullscreenController::class, 'show'])->name('ujian.fullscreen');
    Route::post('/ujian/{mpkId}/{type}/submit', [App\Http\Controllers\UjianFullscreenController::class, 'submit'])->name('ujian.submit');
    Route::post('/ujian/pelanggaran', [App\Http\Controllers\UjianFullscreenController::class, 'logPelanggaran'])->name('ujian.pelanggaran');
});

Route::get('/impersonate/{id}', function ($id) {
    if (!auth()->check() || !auth()->user()?->hasRole('super_admin')) {
        abort(403);
    }
    $targetUser = \App\Models\User::findOrFail($id);
    $impersonatorId = auth()->id();
    
    // Log in the target user to both default guard and Filament guard
    auth()->login($targetUser);
    filament()->auth()->login($targetUser);
    
    // Save the original superadmin ID in session so we can return back
    session(['impersonator_id' => $impersonatorId]);
    
    return redirect()->to('/');
})->name('impersonate');

Route::get('/stop-impersonating', function () {
    if (!session()->has('impersonator_id')) {
        abort(403);
    }
    $user = \App\Models\User::find(session('impersonator_id'));
    
    // Log the superadmin back in to both guards
    auth()->login($user);
    filament()->auth()->login($user);
    
    session()->forget('impersonator_id');
    
    return redirect()->to('/users');
})->name('stop-impersonating');

Route::get('/library/checkin/{nim}', [App\Http\Controllers\LibraryVisitController::class, 'autoCheckin'])->name('library.checkin');
