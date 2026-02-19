<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AkademikKrs;
use App\Models\RiwayatPendidikan;
use App\Models\Kelas;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AkademikKrs>
 */
class AkademikKrsFactory extends Factory
{
    protected $model = AkademikKrs::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_riwayat_pendidikan' => RiwayatPendidikan::factory(),
            'id_kelas' => Kelas::factory(),
            'semester' => $this->faker->numberBetween(1, 8),
            'status_bayar' => 'Y',
            'jumlah_sks' => $this->faker->numberBetween(18, 24),
            'status_aktif' => 'Y',
        ];
    }
}
