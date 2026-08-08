<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Support\SiteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Pemberitahuan ke admin: pesan baru dari formulir kontak situs.
 */
class NewContactMessageAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesan Kontak Baru — '.SiteMail::siteName(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-contact-message',
            with: [
                'contactMessage' => $this->contactMessage,
                'siteName' => SiteMail::siteName(),
                'inboxUrl' => url('/admin/contact-messages'),
            ],
        );
    }
}
