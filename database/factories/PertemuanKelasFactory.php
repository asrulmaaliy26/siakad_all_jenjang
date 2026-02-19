<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MataPelajaranKelas;
use App\Models\PertemuanKelas;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PertemuanKelas>
 */
class PertemuanKelasFactory extends Factory
{
    protected $model = PertemuanKelas::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_mata_pelajaran_kelas' => MataPelajaranKelas::factory(),
            'pertemuan_ke' => $this->faker->numberBetween(1, 16),
            'tanggal' => now(),
            'materi' => $this->faker->sentence(3),
        ];
    }
}
