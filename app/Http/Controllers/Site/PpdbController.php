<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicantRequest;
use App\Mail\NewApplicantAdminMail;
use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\Applicant;
use App\Settings\GeneralSettings;
use App\Support\SiteMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PpdbController extends Controller
{
    /**
     * Landing PPDB: tahun aktif, kartu per jalur, alur pendaftaran, FAQ.
     */
    public function index()
    {
        $year = AcademicYear::active();

        $paths = $year
            ? $year->paths()->where('is_active', true)->orderBy('opens_at')->get()
            : collect();

        return theme_view('ppdb.index', compact('year', 'paths'));
    }

    /**
     * Formulir pendaftaran satu jalur (hanya saat jalur dibuka).
     */
    public function create(AdmissionPath $path)
    {
        if (! $path->isOpen()) {
            return redirect()
                ->route('ppdb.index')
                ->with('ppdb_error', "Pendaftaran jalur {$path->name} sedang tidak dibuka.");
        }

        return theme_view('ppdb.register', compact('path'));
    }

    /**
     * Simpan pendaftaran. Guard jalur tutup + honeypot berjalan lebih dulu
     * di StoreApplicantRequest::prepareForValidation().
     */
    public function store(StoreApplicantRequest $request, AdmissionPath $path)
    {
        $applicant = DB::transaction(function () use ($request, $path): Applicant {
            $applicant = Applicant::create([
                ...$request->safe()->except(['photo', 'documents', 'agreement']),
                'admission_path_id' => $path->id,
                'status' => Applicant::STATUS_PENDING,
            ]);

            $applicant
                ->addMediaFromRequest('photo')
                ->toMediaCollection('photo');

            foreach ($request->file('documents', []) as $document) {
                $applicant
                    ->addMedia($document)
                    ->usingName($document->getClientOriginalName())
                    ->toMediaCollection('documents');
            }

            return $applicant;
        });

        // Beri tahu admin via antrean (setelah transaksi commit);
        // skip bila email sekolah belum diisi.
        if ($adminEmail = SiteMail::adminEmail()) {
            Mail::to($adminEmail)->queue(new NewApplicantAdminMail($applicant));
        }

        // Akses halaman bukti sesaat setelah mendaftar; akses
        // permanen tersedia lewat halaman cek status.
        session(['ppdb_applicant_id' => $applicant->id]);

        return redirect()->route('ppdb.success');
    }

    /**
     * Halaman bukti pendaftaran (hanya untuk sesi yang baru mendaftar).
     */
    public function success()
    {
        $applicant = $this->applicantFromSession();

        if (! $applicant) {
            return redirect()->route('ppdb.index');
        }

        return theme_view('ppdb.success', compact('applicant'));
    }

    public function statusForm()
    {
        return theme_view('ppdb.status', ['applicant' => null]);
    }

    /**
     * Cek status: nomor pendaftaran + tanggal lahir.
     */
    public function checkStatus(Request $request)
    {
        $data = $request->validate([
            'registration_number' => ['required', 'string', 'max:30'],
            'birth_date' => ['required', 'date'],
        ], [], [
            'registration_number' => 'nomor pendaftaran',
            'birth_date' => 'tanggal lahir',
        ]);

        $applicant = $this->findApplicant($data['registration_number'], $data['birth_date']);

        if (! $applicant) {
            return back()
                ->withInput()
                ->withErrors(['registration_number' => 'Data pendaftar tidak ditemukan. Periksa kembali nomor pendaftaran dan tanggal lahir Anda.']);
        }

        return theme_view('ppdb.status', compact('applicant'));
    }

    /**
     * Unduh PDF bukti pendaftaran. Akses: sesi setelah mendaftar, ATAU
     * kombinasi nomor pendaftaran + tanggal lahir yang valid (dari cek status).
     */
    public function receipt(Request $request)
    {
        $applicant = $this->applicantFromSession();

        if (! $applicant && $request->filled(['nomor', 'tanggal_lahir'])) {
            $applicant = $this->findApplicant(
                (string) $request->query('nomor'),
                (string) $request->query('tanggal_lahir'),
            );
        }

        abort_unless($applicant !== null, 403, 'Bukti pendaftaran hanya dapat diunduh setelah mendaftar atau melalui cek status.');

        $applicant->load('path.academicYear');
        $settings = app(GeneralSettings::class);

        $pdf = Pdf::loadView('pdf.ppdb-receipt', [
            'applicant' => $applicant,
            'settings' => $settings,
        ])->setPaper('a4');

        return $pdf->download('bukti-ppdb-'.$applicant->registration_number.'.pdf');
    }

    protected function applicantFromSession(): ?Applicant
    {
        $id = session('ppdb_applicant_id');

        return $id ? Applicant::with('path.academicYear')->find($id) : null;
    }

    protected function findApplicant(string $registrationNumber, string $birthDate): ?Applicant
    {
        return Applicant::query()
            ->with('path.academicYear')
            ->where('registration_number', trim($registrationNumber))
            ->whereDate('birth_date', $birthDate)
            ->first();
    }
}
