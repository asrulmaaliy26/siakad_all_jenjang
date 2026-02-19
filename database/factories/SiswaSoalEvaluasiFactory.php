<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SiswaData;
use App\Models\SiswaSoalEvaluasi;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiswaSoalEvaluasi>
 */
class SiswaSoalEvaluasiFactory extends Factory
{
    protected $model = SiswaSoalEvaluasi::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_soal_evaluasi' => true,
            'id_siswa_data' => SiswaData::factory(),
            'pertanyaan' => $this->faker->sentence(),
            'tipe' => $this->faker->randomElement(['essay', 'pilihan_ganda']),
            'skor' => 10,
            'kunci_jawaban' => 'A',
        ];
    }
}
