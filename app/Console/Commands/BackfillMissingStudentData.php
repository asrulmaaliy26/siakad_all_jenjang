<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RiwayatPendidikan;
use App\Models\AkademikKrs;
use App\Models\TahunAkademik;

class BackfillMissingStudentData extends Command
{
    protected $signature = 'siswa:backfill {--dry-run : Hanya tampilkan, tidak simpan ke database}';
    protected $description = 'Backfill data mahasiswa yang sudah diterima sebelum sistem baru (NIM & KRS)';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('=== DRY RUN MODE — Tidak ada perubahan yang disimpan ===');
        }

        $this->backfillNIM($isDryRun);
        $this->backfillKRS($isDryRun);

        $this->info('Selesai!');
        return self::SUCCESS;
    }

    private function backfillNIM(bool $isDryRun): void
    {
        $this->info("\n[1] Backfill Nomor Induk (NIM) yang kosong...");

        $riwayats = RiwayatPendidikan::whereNull('nomor_induk')
            ->with(['tahunAkademik'])
            ->get();

        $this->info("Ditemukan {$riwayats->count()} riwayat pendidikan tanpa NIM.");

        // Bangun counter awal per tahun akademik berdasarkan NIM yang sudah ada
        $counters = [];

        $bar = $this->output->createProgressBar($riwayats->count());
        $bar->start();

        // Ambil tahun akademik terbaru sebagai default fallback
        $latestTahunAkademik = TahunAkademik::latest()->first();

        foreach ($riwayats as $riwayat) {
            // Gunakan tahun akademik dari riwayat, fallback ke yang terbaru
            $idTahun = $riwayat->id_tahun_akademik ?? $latestTahunAkademik?->id;
            $tahunNama = $riwayat->tahunAkademik?->nama ?? $latestTahunAkademik?->nama ?? date('Y');
            $tahun = substr($tahunNama, 0, 4);

            // Inisialisasi counter per tahun akademik (berdasarkan NIM yang sudah ada di DB)
            if (!isset($counters[$idTahun])) {
                $counters[$idTahun] = RiwayatPendidikan::where('id_tahun_akademik', $idTahun)
                    ->whereNotNull('nomor_induk')
                    ->count();
            }

            $counters[$idTahun]++;
            $nim = $tahun . str_pad($counters[$idTahun], 6, '0', STR_PAD_LEFT);

            if (!$isDryRun) {
                $riwayat->update(['nomor_induk' => $nim]);
            } else {
                $this->line("  [Dry] ID Riwayat {$riwayat->id} (Tahun: {$tahunNama}) → NIM: {$nim}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ NIM selesai di-backfill.");
    }

    private function backfillKRS(bool $isDryRun): void
    {
        $this->info("\n[2] Backfill AkademikKRS yang belum ada...");

        $riwayats = RiwayatPendidikan::whereDoesntHave('akademikKrs')
            ->whereIn('status', ['Y', 'Aktif'])
            ->get();

        $this->info("Ditemukan {$riwayats->count()} riwayat pendidikan tanpa KRS.");

        $tahunAkademikAktif = TahunAkademik::where('status', 'Y')->latest()->first();

        $bar = $this->output->createProgressBar($riwayats->count());
        $bar->start();

        foreach ($riwayats as $riwayat) {
            $idTahun = $riwayat->id_tahun_akademik ?? $tahunAkademikAktif?->id;

            if (!$isDryRun) {
                AkademikKrs::create([
                    'id_riwayat_pendidikan' => $riwayat->id,
                    'jumlah_sks'            => 24,
                    'tgl_krs'               => $riwayat->tanggal_mulai ?? now(),
                    'id_tahun_akademik'     => $idTahun,
                    'status_bayar'          => 'N',
                    'syarat_uts'            => 'N',
                    'syarat_uas'            => 'N',
                    'syarat_krs'            => 'N',
                    'status_aktif'          => 'Y',
                ]);
            } else {
                $this->line("  [Dry] Riwayat ID {$riwayat->id} → akan dibuat KRS (Tahun: {$idTahun})");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ KRS selesai di-backfill.");
    }
}
