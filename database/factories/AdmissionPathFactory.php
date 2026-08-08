<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdmissionPath>
 */
class AdmissionPathFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => 'Jalur '.rtrim(fake()->unique()->words(2, true), '.'),
            'description' => fake()->sentence(),
            'quota' => fake()->numberBetween(20, 100),
            'requirements' => '<ul><li>Kartu Keluarga</li><li>Akta kelahiran</li></ul>',
            'opens_at' => now()->subWeek(),
            'closes_at' => now()->addWeek(),
            'is_active' => true,
        ];
    }

    public function closed(): static
    {
        return $this->state([
            'opens_at' => now()->subMonth(),
            'closes_at' => now()->subWeek(),
        ]);
    }

    public function upcoming(): static
    {
        return $this->state([
            'opens_at' => now()->addWeek(),
            'closes_at' => now()->addMonth(),
        ]);
    }
}
