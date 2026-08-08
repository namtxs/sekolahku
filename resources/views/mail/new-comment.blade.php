<x-mail::message>
# Komentar Baru Menunggu Moderasi

Ada komentar baru di situs **{{ $siteName }}** yang menunggu persetujuan Anda.

**Konten:** {{ $comment->post?->title ?? '-' }}

**Pengirim:** {{ $comment->name }} ({{ $comment->email }})

<x-mail::panel>
{{ $comment->body }}
</x-mail::panel>

<x-mail::button :url="$moderationUrl">
Moderasi Komentar
</x-mail::button>

Hormat kami,<br>
{{ $siteName }}
</x-mail::message>
