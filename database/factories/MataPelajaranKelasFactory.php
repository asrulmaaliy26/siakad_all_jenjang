<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DosenData;
use App\Models\Kelas;
use App\Models\MataPelajaranKurikulum;
use App\Models\RuangKelas;
use App\Models\MataPelajaranKelas;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MataPelajaranKelas>
 */
class MataPelajaranKelasFactory extends Factory
{
    protected $model = MataPelajaranKelas::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_mata_pelajaran_kurikulum' => MataPelajaranKurikulum::factory(),
            'id_kelas' => Kelas::factory(),
            'id_dosen_data' => DosenData::factory(),
            'uts' => now()->addWeeks(6),
            'uas' => now()->addWeeks(12),
            'id_ruang_kelas' => RuangKelas::factory(),
        ];
    }
}
