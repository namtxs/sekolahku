<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\Applicant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uji alur PPDB online: landing, pendaftaran publik, cek status,
 * cetak bukti PDF, dan guard kuota penerimaan.
 *
 * Memakai DatabaseTransactions (bukan RefreshDatabase) karena suite ini
 * berjalan terhadap database utama berisi konten demo — semua mutasi
 * di-rollback setelah tiap test.
 */
class PpdbTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Payload valid formulir pendaftaran (tanpa berkas).
     *
     * @return array<string, mixed>
     */
    protected function validPayload(): array
    {
        return [
            'name' => 'Calon Siswa Uji',
            'nisn' => '1234567890',
            'gender' => 'L',
            'birth_place' => 'Bandung',
            'birth_date' => '2011-04-15',
            'religion' => 'Islam',
            'address' => 'Jl. Pengujian No. 1, Bandung',
            'phone' => '081234567890',
            'email' => 'calon@example.com',
            'school_origin' => 'SMP Negeri 1 Bandung',
            'parent_name' => 'Orang Tua Uji',
            'parent_phone' => '081298765432',
            'avg_score' => 88.5,
            'agreement' => '1',
        ];
    }

    protected function makeOpenPath(array $attributes = []): AdmissionPath
    {
        return AdmissionPath::factory()->create($attributes);
    }

    public function test_landing_page_is_accessible(): void
    {
        $this->get(route('ppdb.index'))
            ->assertOk()
            ->assertSee('PPDB Online');
    }

    public function test_registration_form_shows_for_open_path(): void
    {
        $path = $this->makeOpenPath();

        $this->get(route('ppdb.create', $path->slug))
            ->assertOk()
            ->assertSee('Formulir Pendaftaran Peserta Didik Baru');
    }

    public function test_registration_form_redirects_for_closed_path(): void
    {
        $path = AdmissionPath::factory()->closed()->create();

        $this->get(route('ppdb.create', $path->slug))
            ->assertRedirect(route('ppdb.index'));

        $this->post(route('ppdb.store', $path->slug), $this->validPayload())
            ->assertRedirect(route('ppdb.index'));

        $this->assertDatabaseMissing('applicants', ['nisn' => '1234567890']);
    }

    public function test_valid_submission_creates_applicant_and_shows_receipt(): void
    {
        Storage::fake('public');

        $path = $this->makeOpenPath();

        $response = $this->post(route('ppdb.store', $path->slug), [
            ...$this->validPayload(),
            'photo' => UploadedFile::fake()->image('pas-foto.jpg', 300, 400),
            'documents' => [UploadedFile::fake()->create('kartu-keluarga.pdf', 500, 'application/pdf')],
        ]);

        $response->assertRedirect(route('ppdb.success'));

        $applicant = Applicant::query()->where('nisn', '1234567890')->first();

        $this->assertNotNull($applicant);
        $this->assertSame($path->id, $applicant->admission_path_id);
        $this->assertSame(Applicant::STATUS_PENDING, $applicant->status);
        $this->assertMatchesRegularExpression(
            '/^PPDB-'.$path->academicYear->startYear().'-\d{4}$/',
            $applicant->registration_number,
        );
        $this->assertCount(1, $applicant->getMedia('photo'));
        $this->assertCount(1, $applicant->getMedia('documents'));

        // Halaman bukti dapat diakses lewat sesi yang baru mendaftar.
        $this->get(route('ppdb.success'))
            ->assertOk()
            ->assertSee($applicant->registration_number);
    }

    public function test_duplicate_nisn_in_same_academic_year_is_rejected(): void
    {
        Storage::fake('public');

        $path = $this->makeOpenPath();

        // Jalur lain pada tahun ajaran yang sama.
        $otherPath = $this->makeOpenPath(['academic_year_id' => $path->academic_year_id]);

        Applicant::factory()->create([
            'admission_path_id' => $path->id,
            'nisn' => '1234567890',
        ]);

        $this->post(route('ppdb.store', $otherPath->slug), [
            ...$this->validPayload(),
            'photo' => UploadedFile::fake()->image('pas-foto.jpg'),
        ])->assertSessionHasErrors('nisn');

        $this->assertSame(1, Applicant::query()->where('nisn', '1234567890')->count());
    }

    public function test_status_check_shows_applicant_timeline(): void
    {
        $applicant = Applicant::factory()->verified()->create([
            'verification_note' => 'Berkas lengkap.',
        ]);

        $this->post(route('ppdb.status.check'), [
            'registration_number' => $applicant->registration_number,
            'birth_date' => $applicant->birth_date->toDateString(),
        ])
            ->assertOk()
            ->assertSee($applicant->name)
            ->assertSee('Terverifikasi')
            ->assertSee('Berkas lengkap.');
    }

    public function test_status_check_with_wrong_data_shows_error(): void
    {
        $applicant = Applicant::factory()->create();

        $this->post(route('ppdb.status.check'), [
            'registration_number' => $applicant->registration_number,
            'birth_date' => '1990-01-01', // tanggal lahir salah
        ])->assertSessionHasErrors('registration_number');
    }

    public function test_receipt_pdf_downloads_via_status_check_credentials(): void
    {
        $applicant = Applicant::factory()->create();

        $response = $this->get(route('ppdb.receipt', [
            'nomor' => $applicant->registration_number,
            'tanggal_lahir' => $applicant->birth_date->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_receipt_pdf_is_forbidden_without_session_or_credentials(): void
    {
        $this->get(route('ppdb.receipt'))->assertForbidden();
    }

    public function test_accept_is_blocked_when_quota_is_full(): void
    {
        $path = $this->makeOpenPath(['quota' => 1]);

        Applicant::factory()->accepted()->create(['admission_path_id' => $path->id]);

        $waiting = Applicant::factory()->verified()->create(['admission_path_id' => $path->id]);

        $this->assertFalse($waiting->accept());
        $this->assertSame(Applicant::STATUS_VERIFIED, $waiting->fresh()->status);
    }

    public function test_accept_succeeds_when_quota_is_available(): void
    {
        $path = $this->makeOpenPath(['quota' => 2]);

        Applicant::factory()->accepted()->create(['admission_path_id' => $path->id]);

        $waiting = Applicant::factory()->verified()->create(['admission_path_id' => $path->id]);

        $this->assertTrue($waiting->accept());
        $this->assertSame(Applicant::STATUS_ACCEPTED, $waiting->fresh()->status);
        $this->assertNotNull($waiting->fresh()->decided_at);
        $this->assertSame(0, $path->remainingQuota());
    }
}
