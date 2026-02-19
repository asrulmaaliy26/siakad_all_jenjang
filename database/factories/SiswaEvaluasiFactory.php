<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MataPelajaranKelas;
use App\Models\SiswaJenisEvaluasi;
use App\Models\SiswaEvaluasi;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiswaEvaluasi>
 */
class SiswaEvaluasiFactory extends Factory
{
    protected $model = SiswaEvaluasi::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_mata_pelajaran_kelas' => MataPelajaranKelas::factory(),
            'id_siswa_jenis_evaluasi' => SiswaJenisEvaluasi::factory(),
            'tanggal' => now(),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
