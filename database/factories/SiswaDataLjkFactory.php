<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AkademikKrs;
use App\Models\MataPelajaranKelas;
use App\Models\SiswaDataLJK;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiswaDataLJK>
 */
class SiswaDataLjkFactory extends Factory
{
    protected $model = SiswaDataLJK::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_akademik_krs' => AkademikKrs::factory(),
            'id_mata_pelajaran_kelas' => MataPelajaranKelas::factory(),
            'nilai' => $this->faker->randomFloat(2, 60, 100),
        ];
    }
}
