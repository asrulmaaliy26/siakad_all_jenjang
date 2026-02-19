<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TahunAkademik;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TahunAkademik>
 */
class TahunAkademikFactory extends Factory
{
    protected $model = TahunAkademik::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->year . '/' . ($this->faker->year + 1),
            'periode' => $this->faker->randomElement(['Genap', 'Ganjil']),
            'status' => $this->faker->randomElement(['Y', 'N']),
        ];
    }
}
