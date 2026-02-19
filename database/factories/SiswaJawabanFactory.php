<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SiswaSoalEvaluasi;
use App\Models\AkademikKrs;
use App\Models\SiswaJawaban;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiswaJawaban>
 */
class SiswaJawabanFactory extends Factory
{
    protected $model = SiswaJawaban::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_soal_evaluasi' => SiswaSoalEvaluasi::factory(),
            'id_akademik_krs' => AkademikKrs::factory(),
            'jawaban' => $this->faker->sentence(),
            'skor' => $this->faker->numberBetween(0, 10),
            'waktu_submit' => now(),
        ];
    }
}
