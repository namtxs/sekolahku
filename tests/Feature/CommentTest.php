<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Uji toggle komentar per konten (berita & halaman statis).
 *
 * Memakai DatabaseTransactions (bukan RefreshDatabase) karena suite ini
 * berjalan terhadap database utama berisi konten demo — semua mutasi
 * di-rollback setelah tiap test.
 */
class CommentTest extends TestCase
{
    use DatabaseTransactions;

    /** @var array<string, string> */
    protected array $payload = [
        'name' => 'Pengunjung Uji',
        'email' => 'pengunjung@example.com',
        'body' => 'Komentar percobaan dari test otomatis.',
    ];

    public function test_page_with_comments_enabled_shows_form_and_accepts_comment(): void
    {
        $page = Post::factory()->page()->published()->create(['allow_comments' => true]);

        $this->get('/'.$page->slug)
            ->assertOk()
            ->assertSee('form-komentar', false);

        $this->post(route('comments.store', $page->slug), $this->payload)
            ->assertRedirect(route('pages.show', $page->slug).'#komentar');

        $this->assertDatabaseHas('comments', [
            'post_id' => $page->id,
            'email' => 'pengunjung@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_page_with_comments_disabled_hides_form_and_rejects_post(): void
    {
        $page = Post::factory()->page()->published()->create();

        $this->get('/'.$page->slug)
            ->assertOk()
            ->assertDontSee('form-komentar', false);

        $this->post(route('comments.store', $page->slug), $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('comments', ['post_id' => $page->id]);
    }

    public function test_news_post_accepts_comment_by_default(): void
    {
        $post = Post::factory()->published()->create();

        $this->post(route('comments.store', $post->slug), $this->payload)
            ->assertRedirect(route('posts.show', $post->slug).'#komentar');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'status' => 'pending',
        ]);
    }

    public function test_news_post_with_comments_disabled_rejects_comment(): void
    {
        $post = Post::factory()->published()->create(['allow_comments' => false]);

        $this->post(route('comments.store', $post->slug), $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('comments', ['post_id' => $post->id]);
    }
}
