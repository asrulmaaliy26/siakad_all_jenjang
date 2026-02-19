<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Fakultas;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fakultas>
 */
class FakultasFactory extends Factory
{
    protected $model = Fakultas::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->company . ' Fakultas',
        ];
    }
}
