<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaranKelas;
use App\Models\SiswaData;
use App\Models\SiswaDataLJK;
use App\Models\AkademikKrs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UjianFullscreenController extends Controller
{
    const MAX_PELANGGARAN = 3;

    /**
     * Tampilkan halaman ujian fullscreen
     */
    public function show(Request $request, int $mpkId, string $type)
    {
        $user = Auth::user();

        // Hanya mahasiswa
        if (!$user || !$user->isMurid()) {
            abort(403, 'Halaman ini hanya untuk mahasiswa.');
        }

        $type = in_array(strtolower($type), ['uts', 'uas']) ? strtolower($type) : 'uts';

        /** @var MataPelajaranKelas $mpk */
        $mpk = MataPelajaranKelas::with([
            'mataPelajaranKurikulum.mataPelajaranMaster',
            'dosenData',
            'kelas.tahunAkademik',
        ])->findOrFail($mpkId);

        // Cek status ujian aktif
        $statusCol = $type === 'uas' ? 'status_uas' : 'status_uts';
        if (!$mpk->$statusCol) {
            abort(403, 'Ujian belum dibuka oleh pengajar.');
        }

        // Ambil data siswa & LJK
        $siswa = SiswaData::where('user_id', $user->id)->first();
        if (!$siswa) {
            abort(403, 'Data mahasiswa tidak ditemukan.');
        }

        $ljk = SiswaDataLJK::withoutGlobalScopes()
            ->where('id_mata_pelajaran_kelas', $mpkId)
            ->whereHas('akademikKrs', function ($q) use ($siswa) {
                $q->withoutGlobalScopes()->whereHas('riwayatPendidikan', function ($sq) use ($siswa) {
                    $sq->withoutGlobalScopes()->where('id_siswa_data', $siswa->id);
                });
            })
            ->first();

        if (!$ljk) {
            abort(403, 'Data LJK tidak ditemukan untuk mata pelajaran ini.');
        }

        // Cek apakah cekal ujian
        if ($ljk->cekal_kuliah === 'Y') {
            return view('ujian.blocked', compact('ljk', 'mpk', 'type'));
        }

        // Ambil soal
        $soalFileKey = $type === 'uas' ? 'soal_uas' : 'soal_uts';
        $soalNoteKey = $type === 'uas' ? 'ctt_soal_uas' : 'ctt_soal_uts';
        $fileValue = $mpk->$soalFileKey;
        $filePath = is_array($fileValue) ? ($fileValue[0] ?? null) : $fileValue;
        $fileUrl = $filePath ? asset('storage/' . $filePath) : null;
        $fileExt = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;
        $soalNote = $mpk->$soalNoteKey;

        $jmlPelanggaranCol = $type === 'uas' ? 'jml_pelanggaran_uas' : 'jml_pelanggaran_uts';
        $jmlPelanggaran = $ljk->$jmlPelanggaranCol ?? 0;

        return view('ujian.fullscreen', compact(
            'mpk', 'ljk', 'siswa', 'type',
            'fileUrl', 'fileExt', 'soalNote',
            'jmlPelanggaran'
        ));
    }

    /**
     * Terima laporan pelanggaran via AJAX
     */
    public function logPelanggaran(Request $request)
    {
        $request->validate([
            'ljk_id' => 'required|integer',
            'type'   => 'required|in:uts,uas',
            'keterangan' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        if (!$user || !$user->isMurid()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ljk = SiswaDataLJK::withoutGlobalScopes()->findOrFail($request->ljk_id);

        $type = $request->type;
        $cttCol  = $type === 'uas' ? 'ctt_pelanggaran_uas'  : 'ctt_pelanggaran_uts';
        $jmlCol  = $type === 'uas' ? 'jml_pelanggaran_uas'  : 'jml_pelanggaran_uts';
        $cekalCol = $type === 'uas' ? 'cekal_ujian_uas'      : 'cekal_ujian_uts';

        $jml = ($ljk->$jmlCol ?? 0) + 1;
        $now = now()->format('d/m/Y H:i:s');
        $logBaru = "[{$now}] Pelanggaran ke-{$jml}: {$request->keterangan}";

        $cttLama = $ljk->$cttCol ?? '';
        $cttBaru = $cttLama ? $cttLama . "\n" . $logBaru : $logBaru;

        $cekal = $jml >= self::MAX_PELANGGARAN ? 'Y' : 'N';

        SiswaDataLJK::withoutGlobalScopes()
            ->where('id', $ljk->id)
            ->update([
                $cttCol  => $cttBaru,
                $jmlCol  => $jml,
                $cekalCol => $cekal,
                'cekal_kuliah' => $cekal,
            ]);

        Log::warning("Pelanggaran ujian {$type}", [
            'ljk_id' => $ljk->id,
            'user_id' => $user->id,
            'keterangan' => $request->keterangan,
            'total' => $jml,
            'cekal' => $cekal,
        ]);

        return response()->json([
            'jml_pelanggaran' => $jml,
            'cekal' => $cekal,
            'max' => self::MAX_PELANGGARAN,
        ]);
    }

    /**
     * Submit jawaban ujian
     */
    public function submit(Request $request, int $mpkId, string $type)
    {
        $user = Auth::user();
        if (!$user || !$user->isMurid()) {
            abort(403);
        }

        $type = in_array(strtolower($type), ['uts', 'uas']) ? strtolower($type) : 'uts';
        $siswa = SiswaData::where('user_id', $user->id)->first();

        $ljk = SiswaDataLJK::withoutGlobalScopes()
            ->where('id_mata_pelajaran_kelas', $mpkId)
            ->whereHas('akademikKrs', function ($q) use ($siswa) {
                $q->withoutGlobalScopes()->whereHas('riwayatPendidikan', function ($sq) use ($siswa) {
                    $sq->withoutGlobalScopes()->where('id_siswa_data', $siswa->id);
                });
            })
            ->firstOrFail();

        // Cek cekal
        if ($ljk->cekal_kuliah === 'Y') {
            return back()->with('error', 'Akses ujian Anda telah diblokir karena pelanggaran.');
        }

        $ljkField = $type === 'uas' ? 'ljk_uas' : 'ljk_uts';
        $cttField = $type === 'uas' ? 'ctt_uas'  : 'ctt_uts';
        $tglField = $type === 'uas' ? 'tgl_upload_ljk_uas' : 'tgl_upload_ljk_uts';

        $updateData = [];

        // Handle file upload
        if ($request->hasFile($ljkField)) {
            $existingFiles = is_array($ljk->$ljkField) ? $ljk->$ljkField : json_decode($ljk->$ljkField, true) ?? [];
            $files = $existingFiles;
            
            $dirPath = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $ljk, $ljkField);
            
            foreach ($request->file($ljkField) as $file) {
                $path = $file->store($dirPath, 'public');
                $files[] = $path;
            }
            $updateData[$ljkField] = json_encode($files);
            $updateData[$tglField] = now()->toDateString();
        }

        // Handle text jawaban
        if ($request->filled($cttField)) {
            $updateData[$cttField] = $request->input($cttField);
        }

        if (!empty($updateData)) {
            SiswaDataLJK::withoutGlobalScopes()
                ->where('id', $ljk->id)
                ->update($updateData);
        }

        // Cari referensi Pekan Ujian terkait untuk redirect
        $mpk = \App\Models\MataPelajaranKelas::with('kelas')->findOrFail($mpkId);
        $pekanUjian = \App\Models\PekanUjian::where('id_tahun_akademik', $mpk->kelas->id_tahun_akademik)
            ->where('jenis_ujian', $type)
            ->first();

        $redirectUrl = $pekanUjian ? "/pekan-ujians/{$pekanUjian->id}" : "/admin";

        return redirect($redirectUrl)->with('success', 'Jawaban berhasil dikumpulkan!');
    }
}
