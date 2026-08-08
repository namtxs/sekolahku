@extends('themes.default.layouts.app')

@section('title', 'Bukti Pendaftaran PPDB')

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => 'Pendaftaran Berhasil',
        'subtitle' => 'Simpan nomor pendaftaran Anda untuk memantau status seleksi.',
        'breadcrumbs' => ['PPDB' => route('ppdb.index'), 'Bukti Pendaftaran' => null],
    ])

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Nomor pendaftaran besar --}}
        <section class="rounded-sm border border-slate-300 bg-white text-center shadow-sm">
            <div class="border-b border-slate-200 bg-green-50 px-6 py-5">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-green-100 text-green-700">
                    <svg class="size-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </span>
                <h2 class="mt-3 font-heading text-xl font-bold text-slate-900">Pendaftaran Anda Telah Kami Terima</h2>
                <p class="mt-1 text-sm text-slate-500">{{ tanggal_id($applicant->created_at, 'l, j F Y') }} pukul {{ $applicant->created_at->format('H:i') }} WIB</p>
            </div>
            <div class="px-6 py-7">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Nomor Pendaftaran</p>
                <p class="mt-2 font-heading text-4xl font-bold tracking-wider text-primary-800 sm:text-5xl">{{ $applicant->registration_number }}</p>

                {{-- Tombol salin nomor ke clipboard --}}
                <div x-data="{ copied: false }" class="mt-3">
                    <button type="button"
                        @click="navigator.clipboard.writeText('{{ $applicant->registration_number }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                        class="inline-flex items-center gap-1.5 rounded-sm border border-slate-300 px-3.5 py-1.5 text-xs font-bold text-slate-600 transition hover:border-primary-600 hover:text-primary-700">
                        <svg x-show="! copied" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75h-6a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/></svg>
                        <svg x-cloak x-show="copied" class="size-3.5 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        <span x-text="copied ? 'Tersalin!' : 'Salin Nomor'">Salin Nomor</span>
                    </button>
                </div>

                <p class="mt-4 text-sm text-slate-600">
                    Anda pendaftar ke-<strong>{{ $applicant->queueNumber() }}</strong> pada Jalur {{ $applicant->path->name }}.
                </p>
                <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-600">
                    Catat atau unduh bukti pendaftaran ini. Nomor pendaftaran beserta tanggal lahir
                    digunakan untuk mengecek status seleksi Anda.
                </p>
                <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('ppdb.receipt') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-sm bg-primary-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800 sm:w-auto">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Z"/></svg>
                        Cetak Bukti (PDF)
                    </a>
                    <a href="{{ route('ppdb.status') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-sm border border-primary-600 px-6 py-2.5 text-sm font-bold text-primary-700 transition hover:bg-primary-50 sm:w-auto">
                        Cek Status Pendaftaran
                    </a>
                </div>
            </div>
        </section>

        {{-- Ringkasan data --}}
        <section class="mt-6 rounded-sm border border-slate-300 bg-white shadow-sm">
            @include('themes.default.partials.card-title', ['title' => 'Ringkasan Data Pendaftar'])
            <dl class="divide-y divide-slate-100 px-5 text-sm">
                @foreach([
                    'Jalur' => 'Jalur '.$applicant->path->name.' — PPDB '.$applicant->path->academicYear->name,
                    'Nama Lengkap' => $applicant->name,
                    'NISN' => $applicant->nisn,
                    'Jenis Kelamin' => $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    'Tempat, Tanggal Lahir' => $applicant->birth_place.', '.tanggal_id($applicant->birth_date),
                    'Asal Sekolah' => $applicant->school_origin,
                    'Nama Orang Tua/Wali' => $applicant->parent_name,
                    'Rata-rata Nilai Rapor' => $applicant->avg_score !== null ? number_format((float) $applicant->avg_score, 2, ',', '.') : '-',
                    'Status' => $applicant->statusLabel(),
                ] as $label => $value)
                    <div class="grid gap-1 py-3 sm:grid-cols-3">
                        <dt class="font-semibold text-slate-500">{{ $label }}</dt>
                        <dd class="text-slate-900 sm:col-span-2">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <p class="mt-6 rounded-sm border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-relaxed text-amber-800">
            <strong>Langkah selanjutnya:</strong> panitia akan memverifikasi berkas Anda. Pantau perkembangan
            status melalui halaman <a href="{{ route('ppdb.status') }}" class="font-bold underline hover:text-amber-900">Cek Status</a>
            menggunakan nomor pendaftaran dan tanggal lahir. Hasil seleksi diumumkan setelah pendaftaran
            Jalur {{ $applicant->path->name }} ditutup pada <strong>{{ tanggal_id($applicant->path->closes_at, 'j F Y') }}</strong>.
        </p>
    </div>
@endsection
