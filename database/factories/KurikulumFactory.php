<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use App\Models\JenjangPendidikan;
use App\Models\Kurikulum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kurikulum>
 */
class KurikulumFactory extends Factory
{
    protected $model = Kurikulum::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Kurikulum ' . $this->faker->year,
            'id_jurusan' => Jurusan::factory(),
            'id_tahun_akademik' => TahunAkademik::factory(),
            'id_jenjang_pendidikan' => JenjangPendidikan::factory(),
            'status_aktif' => 'Y',
        ];
    }
}
