<x-mail::message>
@if ($accepted)
# Selamat, Anda Diterima!
@else
# Hasil Seleksi PPDB
@endif

Yth. **{{ $applicant->name }}**,

@if ($accepted)
Selamat! Anda dinyatakan **DITERIMA** sebagai calon peserta didik baru di **{{ $siteName }}** melalui jalur **{{ $applicant->path?->name ?? '-' }}** dengan nomor pendaftaran **{{ $applicant->registration_number }}**.

Silakan pantau informasi daftar ulang melalui situs resmi sekolah atau hubungi panitia PPDB.
@else
Terima kasih telah mengikuti PPDB **{{ $siteName }}**. Setelah melalui proses seleksi, kami sampaikan bahwa pendaftaran Anda dengan nomor **{{ $applicant->registration_number }}** pada jalur **{{ $applicant->path?->name ?? '-' }}** dinyatakan **BELUM DAPAT DITERIMA**.
@endif

@if (filled($applicant->verification_note))
<x-mail::panel>
Catatan panitia: {{ $applicant->verification_note }}
</x-mail::panel>
@endif

<x-mail::button :url="$statusUrl">
Cek Status Pendaftaran
</x-mail::button>

Hormat kami,<br>
Panitia PPDB {{ $siteName }}
</x-mail::message>
