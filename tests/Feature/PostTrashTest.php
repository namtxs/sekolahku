<?php

namespace Tests\Feature;

use App\Filament\Resources\PagePosts\Pages\ListPages;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Uji alur tempat sampah (trash) untuk Berita & Halaman di panel admin:
 * hapus → muncul di filter Terhapus → pulihkan / hapus permanen.
 *
 * Memakai DatabaseTransactions karena suite berjalan terhadap database
 * utama berisi konten demo — semua mutasi di-rollback setelah tiap test.
 */
class PostTrashTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs(User::where('email', 'admin@sekolahku-v2.test')->firstOrFail());
    }

    public function test_trashed_post_is_hidden_by_default_and_visible_via_trashed_filter(): void
    {
        $post = Post::factory()->published()->create();
        $post->delete();

        $this->assertSoftDeleted($post);

        Livewire::test(ListPosts::class)
            ->assertCanNotSeeTableRecords([$post])
            ->filterTable('trashed', false) // "Hanya data yang dihapus"
            ->assertCanSeeTableRecords([$post]);
    }

    public function test_trashed_post_can_be_restored_from_table(): void
    {
        $post = Post::factory()->published()->create();
        $post->delete();

        Livewire::test(ListPosts::class)
            ->filterTable('trashed', false)
            ->callTableAction('restore', $post)
            ->assertHasNoTableActionErrors();

        $this->assertNull($post->fresh()->deleted_at);
    }

    public function test_trashed_post_can_be_force_deleted_from_table(): void
    {
        $post = Post::factory()->published()->create();
        $post->delete();

        Livewire::test(ListPosts::class)
            ->filterTable('trashed', false)
            ->callTableAction('forceDelete', $post)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_trashed_page_can_be_restored_from_table(): void
    {
        $page = Post::factory()->page()->published()->create();
        $page->delete();

        Livewire::test(ListPages::class)
            ->assertCanNotSeeTableRecords([$page])
            ->filterTable('trashed', false)
            ->assertCanSeeTableRecords([$page])
            ->callTableAction('restore', $page)
            ->assertHasNoTableActionErrors();

        $this->assertNull($page->fresh()->deleted_at);
    }
}
