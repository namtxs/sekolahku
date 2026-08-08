@extends('themes.default.layouts.app')

@section('title', 'Kontak')

@push('meta')
    @include('themes.default.partials.meta-og', ['title' => 'Kontak'])
@endpush

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => 'Kontak',
        'subtitle' => 'Hubungi kami untuk pertanyaan, saran, atau informasi lebih lanjut.',
        'breadcrumbs' => ['Kontak' => null],
    ])

    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-5 lg:px-8">
        {{-- Informasi sekolah --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
                @include('themes.default.partials.card-title', ['title' => 'Informasi Kontak'])
                <div class="p-5">
                    <p class="font-heading text-lg font-bold text-slate-900">{{ $settings->site_name ?? config('app.name') }}</p>
                    @if(filled($settings->jenjang ?? null))
                        <p class="mt-0.5 text-sm text-slate-500">{{ $settings->jenjang }}@if(filled($settings->npsn ?? null)) &middot; NPSN {{ $settings->npsn }}@endif</p>
                    @endif

                    <ul class="mt-5 space-y-4 text-sm">
                        @if(filled($settings->address ?? null))
                            <li class="flex gap-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded border border-primary-100 bg-primary-50 text-primary-700">
                                    <svg class="size-4.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd"/></svg>
                                </span>
                                <div>
                                    <p class="font-bold text-slate-900">Alamat</p>
                                    <p class="mt-0.5 text-slate-500">{{ $settings->address }}</p>
                                </div>
                            </li>
                        @endif
                        @if(filled($settings->phone ?? null))
                            <li class="flex gap-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded border border-primary-100 bg-primary-50 text-primary-700">
                                    <svg class="size-4.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 0 0 6.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 0 1 1.767-1.052l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd"/></svg>
                                </span>
                                <div>
                                    <p class="font-bold text-slate-900">Telepon</p>
                                    <p class="mt-0.5 text-slate-500">{{ $settings->phone }}</p>
                                </div>
                            </li>
                        @endif
                        @if(filled($settings->email ?? null))
                            <li class="flex gap-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded border border-primary-100 bg-primary-50 text-primary-700">
                                    <svg class="size-4.5" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
                                </span>
                                <div>
                                    <p class="font-bold text-slate-900">Email</p>
                                    <p class="mt-0.5 text-slate-500">{{ $settings->email }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Jam layanan --}}
            <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
                @include('themes.default.partials.card-title', ['title' => 'Jam Layanan'])
                <ul class="space-y-2 p-5 text-sm text-slate-600">
                    <li class="flex justify-between gap-4"><span>Senin &ndash; Kamis</span><span class="font-semibold text-slate-900">07.00 &ndash; 15.30 WIB</span></li>
                    <li class="flex justify-between gap-4"><span>Jumat</span><span class="font-semibold text-slate-900">07.00 &ndash; 11.30 WIB</span></li>
                    <li class="flex justify-between gap-4"><span>Sabtu &ndash; Minggu &amp; Hari Libur</span><span class="font-semibold text-red-600">Tutup</span></li>
                </ul>
            </div>

            {{-- Peta lokasi (placeholder) --}}
            <div class="rounded-sm border border-slate-300 bg-white shadow-sm">
                @include('themes.default.partials.card-title', ['title' => 'Peta Lokasi'])
                <div class="p-5">
                    <div class="flex aspect-[4/3] flex-col items-center justify-center gap-2 rounded-sm border border-dashed border-slate-300 bg-slate-50 text-slate-400">
                        <svg class="size-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                        <p class="text-sm font-medium">Embed peta Google Maps</p>
                        <p class="max-w-xs text-center text-xs">Tempelkan kode embed peta lokasi sekolah di sini melalui pengaturan tema.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form kontak --}}
        <div class="lg:col-span-3">
            <form action="{{ route('contact.store') }}" method="post" class="rounded-sm border border-slate-300 bg-white shadow-sm">
                @csrf
                @include('themes.default.partials.card-title', ['title' => 'Kirim Pesan'])

                <div class="p-5 sm:p-6">
                    @if(session('contact_status'))
                        <div class="mb-4 rounded border border-primary-200 bg-primary-50 px-4 py-3 text-sm font-medium text-primary-800">
                            {{ session('contact_status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <ul class="list-inside list-disc space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Honeypot anti-spam --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="contact-name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama <span class="text-red-500">*</span></label>
                            <input type="text" id="contact-name" name="name" value="{{ old('name') }}" required
                                class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
                        </div>
                        <div>
                            <label for="contact-email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="contact-email" name="email" value="{{ old('email') }}" required
                                class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
                        </div>
                        <div>
                            <label for="contact-phone" class="mb-1.5 block text-sm font-semibold text-slate-700">Telepon</label>
                            <input type="tel" id="contact-phone" name="phone" value="{{ old('phone') }}"
                                class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
                        </div>
                        <div>
                            <label for="contact-subject" class="mb-1.5 block text-sm font-semibold text-slate-700">Subjek <span class="text-red-500">*</span></label>
                            <input type="text" id="contact-subject" name="subject" value="{{ old('subject') }}" required
                                class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="contact-message" class="mb-1.5 block text-sm font-semibold text-slate-700">Pesan <span class="text-red-500">*</span></label>
                        <textarea id="contact-message" name="message" rows="6" required
                            class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit"
                        class="mt-5 inline-flex items-center gap-2 rounded-sm bg-primary-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-800">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
