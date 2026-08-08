<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'post',
            'title' => rtrim(fake()->unique()->sentence(6), '.'),
            'excerpt' => fake()->optional()->paragraph(),
            'body' => collect(fake()->paragraphs(4))
                ->map(fn (string $p): string => "<p>{$p}</p>")
                ->implode("\n"),
            'status' => 'draft',
            'published_at' => null,
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'views' => 0,
            'allow_comments' => true,
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    /**
     * Halaman statis: tanpa kategori.
     */
    public function page(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'page',
            'category_id' => null,
            'allow_comments' => false,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
