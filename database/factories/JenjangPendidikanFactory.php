<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JenjangPendidikan;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenjangPendidikan>
 */
class JenjangPendidikanFactory extends Factory
{
    protected $model = JenjangPendidikan::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->randomElement(['SMA', 'D3', 'S1', 'S2']),
            'deskripsi' => $this->faker->sentence(5),
        ];
    }
}
