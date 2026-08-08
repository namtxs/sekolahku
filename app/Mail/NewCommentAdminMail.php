<?php

namespace App\Mail;

use App\Models\Comment;
use App\Support\SiteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Pemberitahuan ke admin: komentar baru masuk (status pending).
 */
class NewCommentAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Comment $comment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Komentar Baru Menunggu Moderasi — '.SiteMail::siteName(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-comment',
            with: [
                'comment' => $this->comment,
                'siteName' => SiteMail::siteName(),
                'moderationUrl' => url('/admin/comments'),
            ],
        );
    }
}
