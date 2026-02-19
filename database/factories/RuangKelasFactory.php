<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RuangKelas;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RuangKelas>
 */
class RuangKelasFactory extends Factory
{
    protected $model = RuangKelas::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Ruang ' . $this->faker->randomLetter,
            'deskripsi' => $this->faker->sentence(),
        ];
    }
}
