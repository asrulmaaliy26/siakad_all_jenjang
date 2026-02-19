<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RiwayatPendidikan;
use App\Models\SiswaData;
use App\Models\JenjangPendidikan;
use App\Models\Jurusan;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiwayatPendidikan>
 */
class RiwayatPendidikanFactory extends Factory
{
    protected $model = RiwayatPendidikan::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_siswa_data' => SiswaData::factory(),
            'id_jenjang_pendidikan' => JenjangPendidikan::factory(),
            'id_jurusan' => Jurusan::factory(),
            'status_siswa' => 'Aktif',
            'angkatan' => $this->faker->year,
            'tanggal_mulai' => $this->faker->date(),
            'tanggal_selesai' => null,
        ];
    }
}
