<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'index'])->name('pendaftaran.index');
Route::post('/pendaftaran', [App\Http\Controllers\PendaftaranController::class, 'store'])->name('pendaftaran.store');
