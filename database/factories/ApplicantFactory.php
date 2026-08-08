<?php

namespace Database\Factories;

use App\Models\AdmissionPath;
use App\Models\Applicant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Applicant>
 */
class ApplicantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admission_path_id' => AdmissionPath::factory(),
            'name' => fake('id_ID')->name(),
            'nisn' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_place' => fake('id_ID')->city(),
            'birth_date' => fake()->dateTimeBetween('-16 years', '-14 years')->format('Y-m-d'),
            'religion' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'address' => fake('id_ID')->address(),
            'phone' => '08'.fake()->numerify('##########'),
            'email' => fake()->optional()->safeEmail(),
            'school_origin' => 'SMP Negeri '.fake()->numberBetween(1, 50).' '.fake('id_ID')->city(),
            'parent_name' => fake('id_ID')->name(),
            'parent_phone' => '08'.fake()->numerify('##########'),
            'avg_score' => fake()->optional()->randomFloat(2, 70, 98),
            'status' => Applicant::STATUS_PENDING,
        ];
    }

    public function verified(): static
    {
        return $this->state([
            'status' => Applicant::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state([
            'status' => Applicant::STATUS_ACCEPTED,
            'verified_at' => now()->subDay(),
            'decided_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => Applicant::STATUS_REJECTED,
            'verified_at' => now()->subDay(),
            'decided_at' => now(),
        ]);
    }
}
