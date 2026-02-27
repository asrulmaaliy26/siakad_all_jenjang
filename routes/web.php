<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
use App\Http\Controllers\CetakController;

Route::get('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'index'])->name('pendaftaran.index');
Route::post('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'store'])->name('pendaftaran.store');

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
});

Route::get('/library/checkin/{nim}', [App\Http\Controllers\LibraryVisitController::class, 'autoCheckin'])->name('library.checkin');
