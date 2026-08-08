<x-mail::message>
# Pesan Kontak Baru

Ada pesan baru dari formulir kontak situs **{{ $siteName }}**.

**Nama:** {{ $contactMessage->name }}

**Email:** {{ $contactMessage->email }}

@if (filled($contactMessage->phone))
**Telepon:** {{ $contactMessage->phone }}
@endif

**Subjek:** {{ $contactMessage->subject }}

<x-mail::panel>
{{ $contactMessage->message }}
</x-mail::panel>

<x-mail::button :url="$inboxUrl">
Buka Pesan Masuk
</x-mail::button>

Hormat kami,<br>
{{ $siteName }}
</x-mail::message>
