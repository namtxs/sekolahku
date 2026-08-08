<?php

namespace App\Http\Requests;

use App\Models\AdmissionPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Berjalan sebelum validasi: tolak jalur yang tidak dibuka dan
     * jebak bot lewat honeypot (field "website" tersembunyi).
     */
    protected function prepareForValidation(): void
    {
        /** @var AdmissionPath $path */
        $path = $this->route('path');

        if (! $path->isOpen()) {
            throw new HttpResponseException(
                redirect()
                    ->route('ppdb.index')
                    ->with('ppdb_error', "Pendaftaran jalur {$path->name} sedang tidak dibuka."),
            );
        }

        if (filled($this->input('website'))) {
            // Bot: pura-pura sukses tanpa menyimpan apa pun.
            throw new HttpResponseException(redirect()->route('ppdb.index'));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var AdmissionPath $path */
        $path = $this->route('path');

        return [
            'name' => ['required', 'string', 'max:150'],
            'nisn' => [
                'required',
                'digits:10',
                // NISN unik per tahun ajaran (lintas semua jalur di tahun tsb).
                Rule::unique('applicants', 'nisn')->where(
                    fn ($query) => $query->whereIn(
                        'admission_path_id',
                        AdmissionPath::query()
                            ->where('academic_year_id', $path->academic_year_id)
                            ->pluck('id'),
                    ),
                ),
            ],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'religion' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'school_origin' => ['required', 'string', 'max:150'],
            'parent_name' => ['required', 'string', 'max:150'],
            'parent_phone' => ['required', 'string', 'max:30'],
            'avg_score' => [
                Str::contains($path->slug, 'prestasi') ? 'required' : 'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'documents' => ['nullable', 'array', 'max:5'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'agreement' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nisn.unique' => 'NISN ini sudah terdaftar pada PPDB tahun ajaran ini. Silakan cek status pendaftaran Anda di halaman Cek Status.',
            'avg_score.required' => 'Rata-rata nilai rapor wajib diisi untuk jalur prestasi.',
            'agreement.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'nisn' => 'NISN',
            'gender' => 'jenis kelamin',
            'birth_place' => 'tempat lahir',
            'birth_date' => 'tanggal lahir',
            'religion' => 'agama',
            'address' => 'alamat',
            'phone' => 'nomor HP',
            'email' => 'email',
            'school_origin' => 'asal sekolah',
            'parent_name' => 'nama orang tua/wali',
            'parent_phone' => 'nomor HP orang tua/wali',
            'avg_score' => 'rata-rata nilai rapor',
            'photo' => 'pas foto',
            'documents.*' => 'dokumen',
        ];
    }
}
