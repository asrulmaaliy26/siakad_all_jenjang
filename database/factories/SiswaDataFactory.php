<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SiswaData;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiswaData>
 */
class SiswaDataFactory extends Factory
{
    protected $model = SiswaData::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name,
            'nomor_induk' => $this->faker->unique()->numerify('2024#####'),
        ];
    }
}
