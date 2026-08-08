<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\NewCommentAdminMail;
use App\Models\Post;
use App\Support\SiteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function store(Request $request, string $slug)
    {
        // Berita maupun halaman statis boleh dikomentari, selama
        // toggle allow_comments pada konten tersebut aktif.
        $post = Post::query()->published()->where('slug', $slug)->firstOrFail();

        abort_unless($post->allow_comments, 403, 'Komentar tidak diaktifkan untuk halaman ini.');

        $redirect = ($post->type === 'page'
            ? route('pages.show', $post->slug)
            : route('posts.show', $post->slug)).'#komentar';

        // Honeypot: bot mengisi field tersembunyi ini — pura-pura sukses.
        if (filled($request->input('website'))) {
            return redirect()
                ->to($redirect)
                ->with('comment_status', 'Komentar Anda telah dikirim dan sedang menunggu moderasi.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ], [], [
            'name' => 'nama',
            'email' => 'email',
            'body' => 'komentar',
        ]);

        $comment = $post->comments()->create([
            ...$data,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        // Beri tahu admin via antrean; skip bila email sekolah belum diisi.
        if ($adminEmail = SiteMail::adminEmail()) {
            Mail::to($adminEmail)->queue(new NewCommentAdminMail($comment));
        }

        return redirect()
            ->to($redirect)
            ->with('comment_status', 'Komentar Anda telah dikirim dan sedang menunggu moderasi.');
    }
}
