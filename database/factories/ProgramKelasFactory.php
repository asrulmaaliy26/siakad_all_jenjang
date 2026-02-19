<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProgramKelas;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramKelas>
 */
class ProgramKelasFactory extends Factory
{
    protected $model = ProgramKelas::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->randomElement(['Reguler', 'Karyawan', 'Ekstensi']),
        ];
    }
}
