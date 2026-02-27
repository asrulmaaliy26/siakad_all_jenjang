<?php

namespace App\Http\Controllers;

use App\Models\LibraryVisit;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class LibraryVisitController extends Controller
{
    public function autoCheckin($nim)
    {
        $riwayat = RiwayatPendidikan::where('nomor_induk', $nim)
            ->whereIn('status', ['Y', 'Aktif'])
            ->first();

        if (!$riwayat) {
            return view('library.checkin-status', [
                'status' => 'error',
                'message' => 'Data Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan atau tidak aktif.'
            ]);
        }

        // Cek kunjungan terakhir (mencegah double scan dalam 1 jam)
        $lastVisit = LibraryVisit::where('riwayat_pendidikan_id', $riwayat->id)
            ->where('visited_at', '>', now()->subHour())
            ->first();

        if ($lastVisit) {
            return view('library.checkin-status', [
                'status' => 'success',
                'message' => 'Anda sudah melakukan check-in dalam 1 jam terakhir. Kunjungan Anda sebelumnya sudah tercatat.',
                'student' => $riwayat->siswaData
            ]);
        }

        // Simpan kunjungan
        LibraryVisit::create([
            'riwayat_pendidikan_id' => $riwayat->id,
            'visited_at' => now(),
            'purpose' => 'Kunjungan Mandiri (Scan KTM)',
        ]);

        return view('library.checkin-status', [
            'status' => 'success',
            'message' => 'Berhasil! Kunjungan Anda (' . $riwayat->siswaData->nama . ') telah tercatat.',
            'student' => $riwayat->siswaData
        ]);
    }
}
