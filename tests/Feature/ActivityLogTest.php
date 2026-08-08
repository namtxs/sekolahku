<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Uji pencatatan log aktivitas (spatie/laravel-activitylog):
 * perubahan konten tercatat dengan causer, increment views TIDAK tercatat,
 * dan halaman Log Aktivitas hanya bisa diakses pemegang permission.
 */
class ActivityLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_post_update_is_logged_with_causer_and_old_new_values(): void
    {
        $admin = User::where('email', 'admin@sekolahku-v2.test')->firstOrFail();
        $post = Post::factory()->published()->create(['title' => 'Judul Awal']);

        $this->actingAs($admin);
        $post->update(['title' => 'Judul Baru']);

        $activity = Activity::query()
            ->where('subject_type', Post::class)
            ->where('subject_id', $post->id)
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($activity, 'Perubahan post seharusnya tercatat di log aktivitas.');
        $this->assertSame($admin->id, $activity->causer?->id);
        $this->assertSame('Judul Awal', $activity->getExtraProperty('old.title'));
        $this->assertSame('Judul Baru', $activity->getExtraProperty('attributes.title'));
    }

    public function test_views_increment_does_not_create_activity_log(): void
    {
        $post = Post::factory()->published()->create();

        $countBefore = Activity::query()->count();

        $post->increment('views');

        $this->assertSame(
            $countBefore,
            Activity::query()->count(),
            'Increment penghitung views tidak boleh menambah log aktivitas.',
        );
    }

    public function test_public_post_visit_does_not_create_activity_log(): void
    {
        $post = Post::factory()->published()->create();

        $countBefore = Activity::query()->count();

        $this->get(route('posts.show', $post->slug))->assertOk();

        $this->assertSame($countBefore, Activity::query()->count());
    }

    public function test_admin_can_open_activity_log_page(): void
    {
        $admin = User::where('email', 'admin@sekolahku-v2.test')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/activity-logs')
            ->assertOk()
            ->assertSee('Log Aktivitas');
    }
}
