<?php

namespace Tests\Feature;

use App\Mail\ApplicantDecisionMail;
use App\Mail\NewApplicantAdminMail;
use App\Mail\NewCommentAdminMail;
use App\Mail\NewContactMessageAdminMail;
use App\Models\AdmissionPath;
use App\Models\Applicant;
use App\Models\Post;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uji notifikasi email: komentar baru, pesan kontak, pendaftar PPDB baru
 * (ke admin), dan hasil keputusan PPDB (ke pendaftar). Semua dikirim via
 * antrean (Mail::queue) — di test dicek dengan Mail::fake.
 */
class MailNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected function adminEmail(): string
    {
        return app(GeneralSettings::class)->email;
    }

    public function test_new_comment_queues_mail_to_admin(): void
    {
        Mail::fake();

        $post = Post::factory()->published()->create();

        $this->post(route('comments.store', $post->slug), [
            'name' => 'Pengunjung Uji',
            'email' => 'pengunjung@example.com',
            'body' => 'Komentar percobaan.',
        ])->assertRedirect();

        Mail::assertQueued(
            NewCommentAdminMail::class,
            fn (NewCommentAdminMail $mail): bool => $mail->hasTo($this->adminEmail()),
        );
    }

    public function test_new_contact_message_queues_mail_to_admin(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), [
            'name' => 'Pengunjung Uji',
            'email' => 'pengunjung@example.com',
            'subject' => 'Pertanyaan Uji',
            'message' => 'Isi pesan percobaan.',
        ])->assertRedirect();

        Mail::assertQueued(
            NewContactMessageAdminMail::class,
            fn (NewContactMessageAdminMail $mail): bool => $mail->hasTo($this->adminEmail()),
        );
    }

    public function test_new_ppdb_registration_queues_mail_to_admin(): void
    {
        Mail::fake();
        Storage::fake('public');

        $path = AdmissionPath::factory()->create();

        $this->post(route('ppdb.store', $path->slug), [
            'name' => 'Calon Siswa Uji',
            'nisn' => '9988776655',
            'gender' => 'P',
            'birth_place' => 'Bandung',
            'birth_date' => '2011-02-01',
            'religion' => 'Islam',
            'address' => 'Jl. Pengujian No. 2',
            'phone' => '081234567891',
            'email' => 'calon@example.com',
            'school_origin' => 'SMP Negeri 2 Bandung',
            'parent_name' => 'Orang Tua Uji',
            'parent_phone' => '081298765431',
            'avg_score' => 85,
            'agreement' => '1',
            'photo' => UploadedFile::fake()->image('pas-foto.jpg', 300, 400),
        ])->assertRedirect(route('ppdb.success'));

        Mail::assertQueued(
            NewApplicantAdminMail::class,
            fn (NewApplicantAdminMail $mail): bool => $mail->hasTo($this->adminEmail()),
        );
    }

    public function test_accepting_applicant_queues_decision_mail_to_applicant(): void
    {
        Mail::fake();

        $applicant = Applicant::factory()->verified()->create(['email' => 'calon@example.com']);

        $this->assertTrue($applicant->accept());

        Mail::assertQueued(
            ApplicantDecisionMail::class,
            fn (ApplicantDecisionMail $mail): bool => $mail->hasTo('calon@example.com'),
        );
    }

    public function test_rejecting_applicant_queues_decision_mail_to_applicant(): void
    {
        Mail::fake();

        $applicant = Applicant::factory()->verified()->create(['email' => 'calon@example.com']);

        $applicant->reject('Kuota penuh.');

        Mail::assertQueued(
            ApplicantDecisionMail::class,
            fn (ApplicantDecisionMail $mail): bool => $mail->hasTo('calon@example.com'),
        );
    }

    public function test_decision_mail_is_skipped_when_applicant_has_no_email(): void
    {
        Mail::fake();

        $applicant = Applicant::factory()->verified()->create(['email' => null]);

        $this->assertTrue($applicant->accept());

        Mail::assertNotQueued(ApplicantDecisionMail::class);
    }

    public function test_verifying_applicant_does_not_queue_decision_mail(): void
    {
        Mail::fake();

        $applicant = Applicant::factory()->create(['email' => 'calon@example.com']);

        $applicant->verify('Berkas lengkap.');

        Mail::assertNotQueued(ApplicantDecisionMail::class);
    }

    public function test_admin_mails_are_skipped_when_school_email_is_empty(): void
    {
        Mail::fake();

        $settings = app(GeneralSettings::class);
        $settings->email = null;
        $settings->save();

        $post = Post::factory()->published()->create();

        $this->post(route('comments.store', $post->slug), [
            'name' => 'Pengunjung Uji',
            'email' => 'pengunjung@example.com',
            'body' => 'Komentar percobaan.',
        ])->assertRedirect();

        Mail::assertNotQueued(NewCommentAdminMail::class);
    }
}
