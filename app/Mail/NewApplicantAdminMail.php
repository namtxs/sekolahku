<?php

namespace App\Mail;

use App\Models\Applicant;
use App\Support\SiteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Pemberitahuan ke admin: pendaftar PPDB baru masuk (status pending).
 */
class NewApplicantAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Applicant $applicant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pendaftar PPDB Baru {$this->applicant->registration_number} — ".SiteMail::siteName(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-applicant',
            with: [
                'applicant' => $this->applicant,
                'siteName' => SiteMail::siteName(),
                'detailUrl' => url('/admin/applicants/'.$this->applicant->getKey()),
            ],
        );
    }
}
