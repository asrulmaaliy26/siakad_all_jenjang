<?php

namespace App\Http\Controllers;

use App\Models\AkademikKrs;
use App\Models\DosenData;
use App\Models\MataPelajaranKelas;
use App\Models\PengajuanSurat;
use App\Models\RiwayatPendidikan;
use App\Models\SiswaData;
use App\Models\TaPengajuanJudul;
use App\Models\TaSeminarProposal;
use App\Models\TaSkripsi;
use App\Models\AbsensiSiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CetakController extends Controller
{
    public function absensiKosong($id)
    {
        $kelas = MataPelajaranKelas::with(['dosenData', 'mataPelajaranKurikulum.mataPelajaranMaster', 'kelas.tahunAkademik', 'kelas.programKelas', 'ruangKelas'])->findOrFail($id);
        $krsList = AkademikKrs::whereHas('siswaDataLjk', function ($query) use ($id) {
            $query->where('id_mata_pelajaran_kelas', $id);
        })
            ->with('riwayatPendidikan.siswaData')
            ->get()
            ->sortBy(fn($krs) => $krs->riwayatPendidikan->siswaData->nama ?? '');

        return view('cetak.absensi-kosong', compact('kelas', 'krsList'));
    }

    public function absensiTerisi($id)
    {
        $kelas = MataPelajaranKelas::with(['dosenData', 'mataPelajaranKurikulum.mataPelajaranMaster', 'kelas.tahunAkademik', 'kelas.programKelas', 'ruangKelas'])->findOrFail($id);
        $krsList = AkademikKrs::whereHas('siswaDataLjk', function ($query) use ($id) {
            $query->where('id_mata_pelajaran_kelas', $id);
        })
            ->with('riwayatPendidikan.siswaData')
            ->get()
            ->sortBy(fn($krs) => $krs->riwayatPendidikan->siswaData->nama ?? '');

        $sesiAbsensi = AbsensiSiswa::where('id_mata_pelajaran_kelas', $id)
            ->selectRaw('DATE(waktu_absen) as tanggal')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $absensiData = AbsensiSiswa::where('id_mata_pelajaran_kelas', $id)->get();

        return view('cetak.absensi-terisi', compact('kelas', 'krsList', 'sesiAbsensi', 'absensiData'));
    }

    public function krs($id)
    {
        $krs = AkademikKrs::with([
            'riwayatPendidikan.siswa',
            'riwayatPendidikan.waliDosen',
            'riwayatPendidikan.jurusan',
            'kelas.tahunAkademik',
            'kelas.programKelas',
            'siswaDataLjk.mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster',
            'siswaDataLjk.mataPelajaranKelas.dosenData',
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            $siswa = SiswaData::where('user_id', $user->id)->first();
            if (!$siswa || $krs->riwayatPendidikan?->id_siswa_data !== $siswa->id) {
                abort(403, 'Anda tidak berhak mencetak KRS ini.');
            }
        }

        $totalSksLjk = $krs->siswaDataLjk->map(function ($ljk) {
            return $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
        })->sum();

        $kaprodi = DosenData::where('id_jurusan', $krs->riwayatPendidikan?->id_jurusan)
            ->whereHas('user', fn($q) => $q->role('kaprodi'))
            ->first();

        $pdf = Pdf::loadView('cetak.krs', compact('krs', 'totalSksLjk', 'kaprodi'))->setPaper('a4', 'portrait');

        $namaSiswa = Str::slug($krs->riwayatPendidikan?->siswa?->nama ?? 'krs');
        $semester = $krs->semester ?? 'x';

        return $pdf->stream("KRS-{$namaSiswa}-smt{$semester}.pdf");
    }

    public function pengajuanSurat($id)
    {
        $pengajuan = PengajuanSurat::with([
            'riwayatPendidikan.siswa',
            'riwayatPendidikan.waliDosen',
            'riwayatPendidikan.jurusan',
            'tahunAkademik',
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            $riwayat = RiwayatPendidikan::whereHas('siswa', fn($q) => $q->where('user_id', $user->id))->where('status', 'Aktif')->first();
            if (!$riwayat || $pengajuan->id_riwayat_pendidikan !== $riwayat->id) {
                abort(403, 'Anda tidak berhak mencetak pengajuan ini.');
            }
        }

        $pdf = Pdf::loadView('cetak.pengajuan-surat', compact('pengajuan'))->setPaper('a4', 'portrait');

        $namaSiswa = Str::slug($pengajuan->riwayatPendidikan?->siswa?->nama_lengkap ?? 'pengajuan');
        $jenisStr = Str::slug($pengajuan->jenis_surat);

        return $pdf->stream("Pengajuan-{$jenisStr}-{$namaSiswa}.pdf");
    }

    public function kartuBimbinganSempro($id)
    {
        $sempro = TaSeminarProposal::with([
            'riwayatPendidikan.siswa',
            'riwayatPendidikan.jurusan',
            'tahunAkademik',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'dosenPembimbing3',
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            $riwayat = RiwayatPendidikan::whereHas('siswa', fn($q) => $q->where('user_id', $user->id))->where('status', 'Aktif')->first();
            if (!$riwayat || $sempro->id_riwayat_pendidikan !== $riwayat->id) {
                abort(403, 'Anda tidak berhak mencetak kartu bimbingan ini.');
            }
        }

        $pdf = Pdf::loadView('cetak.kartu-bimbingan-sempro', compact('sempro'))->setPaper('a4', 'portrait');
        $namaSiswa = Str::slug($sempro->riwayatPendidikan?->siswa?->nama ?? 'mahasiswa');
        return $pdf->stream("Kartu-Bimbingan-Sempro-{$namaSiswa}.pdf");
    }

    public function kartuBimbinganJudul($id)
    {
        $ta = TaPengajuanJudul::with([
            'riwayatPendidikan.siswa',
            'riwayatPendidikan.jurusan',
            'tahunAkademik',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'dosenPembimbing3',
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            $riwayat = RiwayatPendidikan::whereHas('siswa', fn($q) => $q->where('user_id', $user->id))->where('status', 'Aktif')->first();
            if (!$riwayat || $ta->id_riwayat_pendidikan !== $riwayat->id) {
                abort(403, 'Anda tidak berhak mencetak kartu bimbingan ini.');
            }
        }

        $pdf = Pdf::loadView('cetak.kartu-bimbingan-judul', compact('ta'))->setPaper('a4', 'portrait');
        $namaSiswa = Str::slug($ta->riwayatPendidikan?->siswa?->nama ?? 'mahasiswa');
        return $pdf->stream("Kartu-Bimbingan-Judul-{$namaSiswa}.pdf");
    }

    public function kartuBimbinganSkripsi($id)
    {
        $ta = TaSkripsi::with([
            'riwayatPendidikan.siswa',
            'riwayatPendidikan.jurusan',
            'tahunAkademik',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'dosenPembimbing3',
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            $riwayat = RiwayatPendidikan::whereHas('siswa', fn($q) => $q->where('user_id', $user->id))->where('status', 'Aktif')->first();
            if (!$riwayat || $ta->id_riwayat_pendidikan !== $riwayat->id) {
                abort(403, 'Anda tidak berhak mencetak kartu bimbingan ini.');
            }
        }

        $pdf = Pdf::loadView('cetak.kartu-bimbingan-skripsi', compact('ta'))->setPaper('a4', 'portrait');
        $namaSiswa = Str::slug($ta->riwayatPendidikan?->siswa?->nama ?? 'mahasiswa');
        return $pdf->stream("Kartu-Bimbingan-Skripsi-{$namaSiswa}.pdf");
    }

    public function khs($id)
    {
        $krs = AkademikKrs::with([
            'riwayatPendidikan.siswa',
            'riwayatPendidikan.jurusan',
            'tahunAkademik',
            'siswaDataLjk.mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster',
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            $siswa = SiswaData::where('user_id', $user->id)->first();
            if (!$siswa || $krs->riwayatPendidikan?->id_siswa_data !== $siswa->id) {
                abort(403, 'Anda tidak berhak mencetak KHS ini.');
            }
        }

        $totalSks = 0;
        $totalBobot = 0;
        foreach ($krs->siswaDataLjk as $ljk) {
            $sks = $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
            $bobot = $ljk->bobot; // Using the new accessor

            $totalSks += $sks;
            $totalBobot += ($bobot * $sks);
        }

        $ips = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        $kaprodi = DosenData::where('id_jurusan', $krs->riwayatPendidikan?->id_jurusan)
            ->whereHas('user', fn($q) => $q->role('kaprodi'))
            ->first();

        $pdf = Pdf::loadView('cetak.khs', compact('krs', 'totalSks', 'totalBobot', 'ips', 'kaprodi'))->setPaper('a4', 'portrait');

        $namaSiswa = Str::slug($krs->riwayatPendidikan?->siswa?->nama ?? 'khs');
        $semester = $krs->semester ?? 'x';

        return $pdf->stream("KHS-{$namaSiswa}-smt{$semester}.pdf");
    }

    public function transkrip($id)
    {
        $siswa = SiswaData::with([
            'riwayatPendidikan.jurusan',
            'riwayatPendidikan.akademikKrs.siswaDataLjk.mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster',
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            if ($siswa->user_id !== $user->id) {
                abort(403, 'Anda tidak berhak mencetak transkrip ini.');
            }
        }

        $allLjk = collect();
        foreach ($siswa->riwayatPendidikan as $riwayat) {
            foreach ($riwayat->akademikKrs as $krs) {
                foreach ($krs->siswaDataLjk as $ljk) {
                    $allLjk->push($ljk);
                }
            }
        }

        $totalSks = 0;
        $totalBobot = 0;
        foreach ($allLjk as $ljk) {
            $sks = $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
            $bobot = $ljk->bobot; // Using the new accessor

            $totalSks += $sks;
            $totalBobot += ($bobot * $sks);
        }

        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        $riwayatAktif = $siswa->riwayatPendidikanAktif;
        $kaprodi = null;
        if ($riwayatAktif) {
            $kaprodi = DosenData::where('id_jurusan', $riwayatAktif->id_jurusan)
                ->whereHas('user', fn($q) => $q->role('kaprodi'))
                ->first();
        }

        $pdf = Pdf::loadView('cetak.transkrip', compact('siswa', 'allLjk', 'totalSks', 'totalBobot', 'ipk', 'kaprodi'))
            ->setPaper('a4', 'portrait');

        $namaSiswa = Str::slug($siswa->nama ?? 'transkrip');

        return $pdf->stream("Transkrip-{$namaSiswa}.pdf");
    }

    public function ktm($id)
    {
        $siswa = SiswaData::with([
            'riwayatPendidikanAktif.jurusan',
            'riwayatPendidikanAktif.programSekolah'
        ])->findOrFail($id);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            if ($siswa->user_id !== $user->id) {
                abort(403, 'Anda tidak berhak mencetak KTM ini.');
            }
        }

        // Pastikan siswa memiliki riwayat aktif
        if (!$siswa->riwayatPendidikanAktif) {
            abort(404, 'Mahasiswa tidak memiliki Riwayat Pendidikan aktif.');
        }

        return view('cetak.ktm', compact('siswa'));
    }
}
