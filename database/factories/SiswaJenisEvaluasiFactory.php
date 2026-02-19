<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SiswaJenisEvaluasi;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiswaJenisEvaluasi>
 */
class SiswaJenisEvaluasiFactory extends Factory
{
    protected $model = SiswaJenisEvaluasi::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->randomElement(['UTS', 'UAS', 'Tugas', 'Quiz']),
            'deskrispsi' => $this->faker->sentence(),
        ];
    }
}
