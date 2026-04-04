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

            $total = 0;
            $count = 0;

            foreach ($fields as $field) {
                $val = $record->{$field};
                // Jika tidak diisi / 0.00 / null maka tidak ikut dirata-rata
                if (!is_null($val) && (float)$val > 0) {
                    $total += (float) $val;
                    $count++;
                }
            }

            if ($count > 0) {
                $average = $total / $count;
                // Batasi maksimal 4.00
                $average = min(4.00, $average);
                $record->Nilai_Akhir = round($average, 2);

                // Map to Nilai_Huruf (Standar A, B, C +/-)
                $record->Nilai_Huruf = self::calculateGradeLetter($average);

                // Otomatis set Status Nilai berdasarkan ambang batas (>= 2.00 Lulus)
                if (empty($record->Status_Nilai) || $record->isDirty('Nilai_UTS', 'Nilai_UAS', 'Nilai_Performance')) {
                    $record->Status_Nilai = ($average >= 2.00) ? 'LULUS' : 'TL';
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
        if ($average >= 3.76 && $average <= 4.00) return 'A+';
        if ($average >= 3.51 && $average <= 3.75) return 'A';
        if ($average >= 3.26 && $average <= 3.50) return 'A-';
        if ($average >= 3.01 && $average <= 3.25) return 'B+';
        if ($average >= 2.76 && $average <= 3.00) return 'B';
        if ($average >= 2.51 && $average <= 2.75) return 'B-';
        if ($average >= 2.26 && $average <= 2.50) return 'C+';
        if ($average >= 2.00 && $average <= 2.25) return 'C';
        if ($average >= 1.76 && $average <= 1.99) return 'C-';
        if ($average >= 0 && $average <= 1.75) return 'D';

        return 'Tidak Valid';
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
    ];

    protected $casts = [
        'nilai'       => 'float',
        'ljk_uts'     => 'array',
        'artikel_uts' => 'array',
        'ljk_uas'     => 'array',
        'artikel_uas' => 'array',
        'ljk_tugas_1' => 'array',
        'ljk_tugas_2' => 'array',
        'ljk_tugas_3' => 'array',
        'ljk_tugas_4' => 'array',
        'ljk_tugas_5' => 'array',
        'ljk_tugas_6' => 'array',
        'ljk_tugas_7' => 'array',
        'ljk_tugas_8' => 'array',
        'ljk_tugas_9' => 'array',
        'ljk_tugas_10' => 'array',
        'ljk_tugas_11' => 'array',
        'ljk_tugas_12' => 'array',
    ];

    /* ================= RELATIONS ================= */
    public function getBobotAttribute()
    {
        return $this->Nilai_Akhir ?? 0.0;
    }

    public function akademikKrs()
    {
        return $this->belongsTo(AkademikKrs::class, 'id_akademik_krs');
    }

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(
            MataPelajaranKelas::class,
            'id_mata_pelajaran_kelas'
        );
    }
}
