<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('comments.view_any');
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->can('comments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('comments.create');
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->can('comments.update');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->can('comments.delete');
    }
}
