<x-mail::message>
# Pendaftar PPDB Baru

Ada pendaftar baru pada PPDB **{{ $siteName }}** yang menunggu verifikasi berkas.

**Nomor Pendaftaran:** {{ $applicant->registration_number }}

**Nama:** {{ $applicant->name }}

**Jalur:** {{ $applicant->path?->name ?? '-' }}

**Asal Sekolah:** {{ $applicant->school_origin }}

<x-mail::button :url="$detailUrl">
Lihat Detail Pendaftar
</x-mail::button>

Hormat kami,<br>
{{ $siteName }}
</x-mail::message>
