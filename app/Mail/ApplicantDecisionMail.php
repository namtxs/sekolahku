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
 * Pemberitahuan ke pendaftar PPDB: hasil keputusan (diterima/ditolak).
 */
class ApplicantDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Applicant $applicant) {}

    public function envelope(): Envelope
    {
        $subject = $this->applicant->status === Applicant::STATUS_ACCEPTED
            ? "Selamat, Anda Diterima! Hasil PPDB {$this->applicant->registration_number}"
            : "Hasil Seleksi PPDB {$this->applicant->registration_number}";

        return new Envelope(subject: $subject.' — '.SiteMail::siteName());
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.applicant-decision',
            with: [
                'applicant' => $this->applicant,
                'accepted' => $this->applicant->status === Applicant::STATUS_ACCEPTED,
                'siteName' => SiteMail::siteName(),
                'statusUrl' => route('ppdb.status'),
            ],
        );
    }
}
