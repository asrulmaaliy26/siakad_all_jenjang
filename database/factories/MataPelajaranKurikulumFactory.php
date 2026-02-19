<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kurikulum;
use App\Models\MataPelajaranMaster;
use App\Models\MataPelajaranKurikulum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MataPelajaranKurikulum>
 */
class MataPelajaranKurikulumFactory extends Factory
{
    protected $model = MataPelajaranKurikulum::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_kurikulum' => Kurikulum::factory(),
            'id_mata_pelajaran_master' => MataPelajaranMaster::factory(),
            'semester' => $this->faker->numberBetween(1, 8),
        ];
    }
}
