<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DosenData;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DosenData>
 */
class DosenDataFactory extends Factory
{
    protected $model = DosenData::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name,
        ];
    }
}
