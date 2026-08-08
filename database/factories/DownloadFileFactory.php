<?php

namespace Database\Factories;

use App\Models\DownloadFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DownloadFile>
 */
class DownloadFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->unique()->sentence(5), '.'),
            'description' => fake()->optional()->paragraph(),
            'category' => fake()->optional()->randomElement(['Formulir', 'Kurikulum', 'Pengumuman', 'Lainnya']),
            'download_count' => 0,
            'is_published' => true,
            'user_id' => User::factory(),
        ];
    }
}
