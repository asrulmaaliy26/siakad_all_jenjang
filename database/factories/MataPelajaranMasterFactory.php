<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Jurusan;
use App\Models\MataPelajaranMaster;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MataPelajaranMaster>
 */
class MataPelajaranMasterFactory extends Factory
{
    protected $model = MataPelajaranMaster::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Algoritma', 'Basis Data', 'Jaringan', 'AI']),
            'id_jurusan' => Jurusan::factory(),
            'bobot' => $this->faker->numberBetween(2, 4),
            'jenis' => $this->faker->randomElement(['wajib', 'peminatan']),
        ];
    }
}
