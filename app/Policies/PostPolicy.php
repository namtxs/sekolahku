<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

/**
 * Permission dicek berdasarkan tipe record: `posts.*` untuk type=post,
 * `pages.*` untuk type=page.
 *
 * Keterbatasan: viewAny dan create tidak punya instance Post sehingga selalu
 * memakai `posts.*` — pemisahan listing/form posts vs pages di level resource
 * (Filament) perlu penanganan sendiri nanti.
 */
class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('posts.view_any');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can($this->module($post).'.view');
    }

    public function create(User $user): bool
    {
        return $user->can('posts.create');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can($this->module($post).'.update')
            && $this->ownsOrManages($user, $post);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can($this->module($post).'.delete')
            && $this->ownsOrManages($user, $post);
    }

    public function restore(User $user, Post $post): bool
    {
        return $this->delete($user, $post);
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $this->delete($user, $post);
    }

    protected function module(Post $post): string
    {
        return $post->type === 'page' ? 'pages' : 'posts';
    }

    /**
     * Penulis hanya boleh mengubah/menghapus tulisannya sendiri;
     * Admin dan Operator boleh semua (Super Admin lolos via Gate::before).
     */
    protected function ownsOrManages(User $user, Post $post): bool
    {
        return $post->user_id === $user->id
            || $user->hasAnyRole(['Admin', 'Operator']);
    }
}
