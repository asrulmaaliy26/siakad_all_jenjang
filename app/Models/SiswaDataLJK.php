<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaDataLJK extends Model
{
    use HasFactory;

    protected $table = 'siswa_data_ljk';

    protected static function booted()
    {
        static::saving(function ($record) {
            $fields = [
                'nilai', // Add 'nilai' to be processed for comma replacement
                'Nilai_UTS',
                'Nilai_UAS',
                'Nilai_Performance',
                'Nilai_TGS_1',
                'Nilai_TGS_2',
                'Nilai_TGS_3',
                'Nilai_TGS_4',
                'Nilai_TGS_5',
                'Nilai_TGS_6',
                'Nilai_TGS_7',
                'Nilai_TGS_8',
                'Nilai_TGS_9',
                'Nilai_TGS_10',
                'Nilai_TGS_11',
                'Nilai_TGS_12',
            ];

            // Normalize comma to dot for all float fields before calculation
            foreach ($fields as $field) {
                if (isset($record->{$field}) && is_string($record->{$field}) && strpos($record->{$field}, ',') !== false) {
                    $record->{$field} = str_replace(',', '.', $record->{$field});
                }
            }

            // Hitung rata-rata tugas terlebih dahulu
            $tugasFields = [
                'Nilai_TGS_1', 'Nilai_TGS_2', 'Nilai_TGS_3', 'Nilai_TGS_4',
                'Nilai_TGS_5', 'Nilai_TGS_6', 'Nilai_TGS_7', 'Nilai_TGS_8',
                'Nilai_TGS_9', 'Nilai_TGS_10', 'Nilai_TGS_11', 'Nilai_TGS_12'
            ];
            
            $totalTugas = 0;
            $countTugas = 0;
            foreach ($tugasFields as $field) {
                $val = $record->{$field};
                // Jika tidak diisi / 0.00 / null maka tidak ikut dirata-rata
                if (!is_null($val) && (float)$val > 0) {
                    $totalTugas += (float) $val;
                    $countTugas++;
                }
            }
            $rataRataTugas = $countTugas > 0 ? ($totalTugas / $countTugas) : 0;

            // 4 Komponen Wajib: UTS, UAS, Performance, Rata-rata Tugas (skala 0-100)
            $uts = (float) ($record->Nilai_UTS ?? 0);
            $uas = (float) ($record->Nilai_UAS ?? 0);
            $perf = (float) ($record->Nilai_Performance ?? 0);

            // Hitung rata-rata komponen (hanya komponen yang memiliki nilai > 0 yang dihitung, atau jika didefinisikan semuanya dibagi 4)
            // Standar: Asumsi input dosen adalah 0-100.
            if ($uts > 0 || $uas > 0 || $perf > 0 || $rataRataTugas > 0) {
                // Jika ingin menggunakan pembagi dinamis (hanya komponen yang ada):
                $komponen = [];
                if ($uts > 0) $komponen[] = $uts;
                if ($uas > 0) $komponen[] = $uas;
                if ($perf > 0) $komponen[] = $perf;
                if ($rataRataTugas > 0) $komponen[] = $rataRataTugas;

                // Rata-rata skala 100
                $average100 = array_sum($komponen) / count($komponen);
                
                $record->Nilai_Akhir = round($average100, 2); // Simpan skala 100 di Nilai_Akhir

                // Map to Nilai_Huruf (Standar A, B, C +/-)
                $record->Nilai_Huruf = self::calculateGradeLetter($average100);

                // Otomatis set Status Nilai berdasarkan ambang batas (>= C Lulus)
                if (empty($record->Status_Nilai) || $record->isDirty('Nilai_UTS', 'Nilai_UAS', 'Nilai_Performance', 'Nilai_TGS_1')) {
                    if ($average100 > 0 && $average100 <= 4.00) {
                        $record->Status_Nilai = ($average100 >= 2.00) ? 'LULUS' : 'TL';
                    } else {
                        $record->Status_Nilai = ($average100 >= 55.00) ? 'LULUS' : 'TL';
                    }
                }
            } else {
                // Jika tidak ada nilai sama sekali
                $record->Nilai_Akhir = 0;
                $record->Nilai_Huruf = 'E'; 
                $record->Status_Nilai = 'TL';
            }
        });
    }

    public static function calculateGradeLetter($average)
    {
        if ($average <= 0) return 'E';

        // Deteksi Skala 4.00
        if ($average <= 4.00) {
            return match (true) {
                $average >= 3.80 => 'A',
                $average >= 3.60 => 'A-',
                $average >= 3.30 => 'B+',
                $average >= 3.00 => 'B',
                $average >= 2.75 => 'B-',
                $average >= 2.50 => 'C+',
                $average >= 2.00 => 'C',
                $average >= 1.75 => 'C-',
                $average >= 1.00 => 'D',
                default          => 'E',
            };
        }

        // Skala 100
        return match (true) {
            $average >= 85 => 'A',
            $average >= 80 => 'A-',
            $average >= 75 => 'B+',
            $average >= 70 => 'B',
            $average >= 65 => 'B-',
            $average >= 60 => 'C+',
            $average >= 55 => 'C',
            $average >= 50 => 'C-',
            $average >= 40 => 'D',
            default        => 'E',
        };
    }

    public static function getBobotDariHuruf($huruf)
    {
        return match ($huruf) {
            'A', 'A+' => 4.00,
            'A-' => 3.75,
            'B+' => 3.50,
            'B'  => 3.00,
            'B-' => 2.75,
            'C+' => 2.50,
            'C'  => 2.00,
            'C-' => 1.75,
            'D'  => 1.00,
            'E'  => 0.00,
            default => 0.00,
        };
    }


    // protected $primaryKey = 'id_data_ljk';

    protected $fillable = [
        'id_akademik_krs',
        'id_mata_pelajaran_kelas',
        'nilai',
        'ljk_simulasi',
        'ljk_uas',
        'artikel_uas',
        'tgl_upload_ljk_uas',
        'tgl_upload_artikel_uas',
        'ljk_uts',
        'artikel_uts',
        'tgl_upload_ljk_uts',
        'tgl_upload_artikel_uas',
        'tugas',
        'tgl_upload_tugas',
        'ljk_tugas_1',
        'ctt_tugas_1',
        'ljk_tugas_2',
        'ctt_tugas_2',
        'ljk_tugas_3',
        'ctt_tugas_3',
        'Nilai_UTS',
        'Nilai_TGS_1',
        'Nilai_TGS_2',
        'Nilai_TGS_3',
        'ljk_tugas_4',
        'ctt_tugas_4',
        'Nilai_TGS_4',
        'ljk_tugas_5',
        'ctt_tugas_5',
        'Nilai_TGS_5',
        'ljk_tugas_6',
        'ctt_tugas_6',
        'Nilai_TGS_6',
        'ljk_tugas_7',
        'ctt_tugas_7',
        'Nilai_TGS_7',
        'ljk_tugas_8',
        'ctt_tugas_8',
        'Nilai_TGS_8',
        'ljk_tugas_9',
        'ctt_tugas_9',
        'Nilai_TGS_9',
        'ljk_tugas_10',
        'ctt_tugas_10',
        'Nilai_TGS_10',
        'ljk_tugas_11',
        'ctt_tugas_11',
        'Nilai_TGS_11',
        'ljk_tugas_12',
        'ctt_tugas_12',
        'Nilai_TGS_12',
        'Nilai_UAS',
        'Nilai_Performance',
        'Nilai_Akhir',
        'Nilai_Huruf',
        'Status_Nilai',
        'Rekom_Nilai',
        'ket',
        'transfer',
        'cekal_kuliah',
        'ctt_uts',
        'ctt_uas',
        'ctt_pelanggaran_uts',
        'ctt_pelanggaran_uas',
        'jml_pelanggaran_uts',
        'jml_pelanggaran_uas',
        'cekal_ujian_uts',
        'cekal_ujian_uas',
    ];

    protected $casts = [
        'ljk_simulasi'      => 'array',
        'ljk_uas'           => 'array',
        'artikel_uas'       => 'array',
        'ljk_uts'           => 'array',
        'artikel_uts'       => 'array',
        'tugas'             => 'array',
        'ljk_tugas_1'       => 'array',
        'ljk_tugas_2'       => 'array',
        'ljk_tugas_3'       => 'array',
        'ljk_tugas_4'       => 'array',
        'ljk_tugas_5'       => 'array',
        'ljk_tugas_6'       => 'array',
        'ljk_tugas_7'       => 'array',
        'ljk_tugas_8'       => 'array',
        'ljk_tugas_9'       => 'array',
        'ljk_tugas_10'      => 'array',
        'ljk_tugas_11'      => 'array',
        'ljk_tugas_12'      => 'array',
        'Nilai_UTS'         => 'decimal:2',
        'Nilai_UAS'         => 'decimal:2',
        'Nilai_TGS_1'       => 'decimal:2',
        'Nilai_TGS_2'       => 'decimal:2',
        'Nilai_TGS_3'       => 'decimal:2',
        'Nilai_TGS_4'       => 'decimal:2',
        'Nilai_TGS_5'       => 'decimal:2',
        'Nilai_TGS_6'       => 'decimal:2',
        'Nilai_TGS_7'       => 'decimal:2',
        'Nilai_TGS_8'       => 'decimal:2',
        'Nilai_TGS_9'       => 'decimal:2',
        'Nilai_TGS_10'      => 'decimal:2',
        'Nilai_TGS_11'      => 'decimal:2',
        'Nilai_TGS_12'      => 'decimal:2',
        'Nilai_Performance' => 'decimal:2',
        'Nilai_Akhir'       => 'decimal:2',
        'tgl_upload_ljk_uas'     => 'date',
        'tgl_upload_artikel_uas' => 'date',
        'tgl_upload_ljk_uts'     => 'date',
        'tgl_upload_tugas'       => 'date',
    ];

    /* ================= RELATIONS ================= */
    public function getBobotAttribute()
    {
        // Jika nilai akhir sudah berada dalam rentang skala 4.00, 
        // gunakan nilai akhir asli secara persis sebagai Angka Mutu (AM).
        if ($this->Nilai_Akhir > 0 && $this->Nilai_Akhir <= 4.00) {
            return $this->Nilai_Akhir;
        }
        
        // Jika menggunakan skala 100, konversi dari Huruf Mutu
        return self::getBobotDariHuruf($this->Nilai_Huruf);
    }

    public function akademikKrs()
    {
        return $this->belongsTo(AkademikKrs::class, 'id_akademik_krs');
    }

    public function taSkripsi()
    {
        return $this->hasOneThrough(
            TaSkripsi::class,
            AkademikKrs::class,
            'id', // AkademikKrs.id
            'id_riwayat_pendidikan', // TaSkripsi.id_riwayat_pendidikan
            'id_akademik_krs', // SiswaDataLJK.id_akademik_krs
            'id_riwayat_pendidikan' // AkademikKrs.id_riwayat_pendidikan
        );
    }

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(
            MataPelajaranKelas::class,
            'id_mata_pelajaran_kelas'
        );
    }
}
