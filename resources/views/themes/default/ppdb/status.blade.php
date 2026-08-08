@extends('themes.default.layouts.app')

@section('title', 'Cek Status PPDB')

@php($inputClass = 'w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600')

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => 'Cek Status Pendaftaran',
        'subtitle' => 'Masukkan nomor pendaftaran dan tanggal lahir untuk melihat perkembangan seleksi PPDB Anda.',
        'breadcrumbs' => ['PPDB' => route('ppdb.index'), 'Cek Status' => null],
    ])

    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <form action="{{ route('ppdb.status.check') }}" method="post" class="rounded-sm border border-slate-300 bg-white shadow-sm">
            @csrf
            @include('themes.default.partials.card-title', ['title' => 'Formulir Cek Status'])
            <div class="p-5 sm:p-6">
                @if($errors->any())
                    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-inside list-disc space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="registration_number" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor Pendaftaran <span class="text-red-500">*</span></label>
                        <input type="text" id="registration_number" name="registration_number"
                            value="{{ old('registration_number', $applicant?->registration_number) }}" required
                            placeholder="PPDB-2026-0001" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label for="birth_date" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" id="birth_date" name="birth_date"
                            value="{{ old('birth_date', $applicant?->birth_date?->toDateString()) }}" required class="{{ $inputClass }}">
                    </div>
                </div>
                <button type="submit"
                    class="mt-5 inline-flex items-center gap-2 rounded-sm bg-primary-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-800">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    Cek Status
                </button>
            </div>
        </form>

        @if($applicant)
            @php($statusOrder = ['pending' => 1, 'verified' => 2, 'accepted' => 3, 'rejected' => 3])
            @php($currentStep = $statusOrder[$applicant->status] ?? 1)
            @php($isRejected = $applicant->status === 'rejected')

            <section class="mt-8 rounded-sm border border-slate-300 bg-white shadow-sm">
                @include('themes.default.partials.card-title', ['title' => 'Hasil Pemeriksaan'])
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-heading text-lg font-bold text-slate-900">{{ $applicant->name }}</p>
                            <p class="mt-0.5 text-sm text-slate-500">
                                {{ $applicant->registration_number }} &middot; Jalur {{ $applicant->path->name }} &middot; PPDB {{ $applicant->path->academicYear->name }}
                                &middot; Pendaftar ke-{{ $applicant->queueNumber() }}
                            </p>
                        </div>
                        @php($badge = match ($applicant->status) {
                            'pending' => ['Menunggu Verifikasi', 'bg-slate-100 text-slate-700 border-slate-300'],
                            'verified' => ['Terverifikasi', 'bg-sky-100 text-sky-800 border-sky-300'],
                            'accepted' => ['Diterima', 'bg-green-100 text-green-800 border-green-300'],
                            'rejected' => ['Ditolak', 'bg-red-100 text-red-700 border-red-300'],
                            default => [$applicant->status, 'bg-slate-100 text-slate-700 border-slate-300'],
                        })
                        <span class="inline-flex w-fit items-center rounded-full border px-3.5 py-1.5 text-sm font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
                    </div>

                    {{-- Timeline status --}}
                    <ol class="mt-7 space-y-0">
                        @foreach([
                            [1, 'Pendaftaran Diterima', 'Formulir dan berkas Anda telah masuk ke sistem.', tanggal_id($applicant->created_at, 'j F Y').' '.$applicant->created_at->format('H:i')],
                            [2, 'Verifikasi Berkas', $currentStep >= 2 ? 'Berkas Anda dinyatakan lengkap dan sah oleh panitia.' : 'Panitia sedang memeriksa kelengkapan berkas Anda.', $applicant->verified_at ? tanggal_id($applicant->verified_at, 'j F Y').' '.$applicant->verified_at->format('H:i') : null],
                            [3, $isRejected ? 'Hasil Seleksi: Tidak Diterima' : 'Hasil Seleksi'.($applicant->status === 'accepted' ? ': Diterima' : ''), match (true) {
                                $applicant->status === 'accepted' => 'Selamat! Anda dinyatakan diterima. Ikuti informasi daftar ulang dari sekolah.',
                                $isRejected => 'Mohon maaf, Anda belum dapat kami terima pada jalur ini.',
                                default => 'Keputusan akhir akan diumumkan setelah proses verifikasi selesai.',
                            }, $applicant->decided_at ? tanggal_id($applicant->decided_at, 'j F Y').' '.$applicant->decided_at->format('H:i') : null],
                        ] as [$step, $stepTitle, $stepBody, $stepTime])
                            @php($reached = $currentStep >= $step)
                            @php($failed = $step === 3 && $isRejected)
                            <li class="relative flex gap-4 pb-8 last:pb-0">
                                @unless($loop->last)
                                    <span class="absolute left-[15px] top-8 h-full w-0.5 {{ $currentStep > $step ? 'bg-primary-600' : 'bg-slate-200' }}" aria-hidden="true"></span>
                                @endunless
                                <span class="z-10 flex size-8 shrink-0 items-center justify-center rounded-full border-2
                                    {{ $failed ? 'border-red-500 bg-red-500 text-white' : ($reached ? 'border-primary-600 bg-primary-600 text-white' : 'border-slate-300 bg-white text-slate-400') }}">
                                    @if($failed)
                                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    @elseif($reached)
                                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    @else
                                        <span class="text-xs font-bold">{{ $step }}</span>
                                    @endif
                                </span>
                                <div class="min-w-0 pt-1">
                                    <p class="text-sm font-bold {{ $failed ? 'text-red-700' : ($reached ? 'text-slate-900' : 'text-slate-400') }}">{{ $stepTitle }}</p>
                                    <p class="mt-0.5 text-sm {{ $reached ? 'text-slate-600' : 'text-slate-400' }}">{{ $stepBody }}</p>
                                    @if($stepTime && $reached)
                                        <p class="mt-1 text-xs text-slate-400">{{ $stepTime }} WIB</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    @if(filled($applicant->verification_note))
                        <div class="mt-5 rounded-sm border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            <p class="font-bold">Catatan panitia:</p>
                            <p class="mt-1">{{ $applicant->verification_note }}</p>
                        </div>
                    @endif

                    @if(! $applicant->decided_at)
                        <p class="mt-5 rounded-sm border border-sky-200 bg-sky-50 px-4 py-3 text-sm leading-relaxed text-sky-800">
                            Hasil seleksi diumumkan di halaman ini setelah pendaftaran Jalur {{ $applicant->path->name }}
                            ditutup pada <strong>{{ tanggal_id($applicant->path->closes_at, 'j F Y') }}</strong>.
                            Silakan cek kembali secara berkala.
                        </p>
                    @endif

                    <div class="mt-6 border-t border-slate-200 pt-5">
                        <a href="{{ route('ppdb.receipt', ['nomor' => $applicant->registration_number, 'tanggal_lahir' => $applicant->birth_date->toDateString()]) }}"
                            class="inline-flex items-center gap-2 rounded-sm bg-primary-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            Cetak Bukti Pendaftaran (PDF)
                        </a>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
