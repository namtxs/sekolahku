@extends('themes.default.layouts.app')

@section('title', 'PPDB Online')

@push('meta')
    @include('themes.default.partials.meta-og', ['title' => 'PPDB Online'])
@endpush

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => 'PPDB Online'.($year ? ' '.$year->name : ''),
        'subtitle' => 'Penerimaan Peserta Didik Baru '.($settings->site_name ?? config('app.name')).' secara daring: pilih jalur, isi formulir, unggah berkas, dan pantau status seleksi.',
        'breadcrumbs' => ['PPDB' => null],
    ])

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @if(session('ppdb_error'))
            <div class="mb-6 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
                {{ session('ppdb_error') }}
            </div>
        @endif

        @if(! $year)
            @include('themes.default.partials.empty-state', [
                'title' => 'PPDB belum dibuka.',
                'message' => 'Tahun pelajaran penerimaan peserta didik baru belum ditetapkan. Silakan kunjungi halaman ini kembali atau hubungi sekolah untuk informasi lebih lanjut.',
            ])
        @else
            {{-- Banner info tahun aktif --}}
            <section class="overflow-hidden rounded-sm border border-slate-300 bg-primary-800 text-white shadow-sm">
                <div class="flex flex-col gap-6 px-6 py-8 sm:px-8 md:flex-row md:items-center md:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-400">Penerimaan Peserta Didik Baru</p>
                        <h2 class="mt-2 font-heading text-2xl font-bold sm:text-3xl">Tahun Pelajaran {{ $year->name }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-primary-100">
                            Pendaftaran dilakukan sepenuhnya secara daring melalui halaman ini.
                            Siapkan berkas dalam bentuk digital (pas foto, kartu keluarga, akta kelahiran, dan rapor),
                            pilih jalur yang sesuai, lalu isi formulir pendaftaran dengan data yang benar.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('ppdb.status') }}"
                            class="inline-flex items-center gap-2 rounded-sm bg-amber-400 px-5 py-3 text-sm font-bold text-primary-950 shadow-sm transition hover:bg-amber-300">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                            Cek Status Pendaftaran
                        </a>
                    </div>
                </div>
            </section>

            {{-- Kartu jalur --}}
            <div class="mt-10">
                @include('themes.default.partials.section-title', ['title' => 'Jalur Pendaftaran'])

                @if($paths->isEmpty())
                    @include('themes.default.partials.empty-state', [
                        'title' => 'Belum ada jalur pendaftaran.',
                        'message' => 'Jalur PPDB untuk tahun pelajaran ini akan diumumkan di halaman ini.',
                    ])
                @else
                    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($paths as $path)
                            @php($open = $path->isOpen())
                            <div class="flex flex-col rounded-sm border border-slate-300 bg-white shadow-sm transition hover:shadow-md">
                                <div class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-5 py-3.5">
                                    <h3 class="font-heading text-base font-bold text-primary-800">Jalur {{ $path->name }}</h3>
                                    @if($open)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-800">
                                            <span class="size-1.5 rounded-full bg-green-500"></span> Dibuka
                                        </span>
                                    @elseif($path->isUpcoming())
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-800">
                                            <span class="size-1.5 rounded-full bg-sky-500"></span> Segera
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-800">
                                            <span class="size-1.5 rounded-full bg-red-500"></span> Ditutup
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-1 flex-col p-5">
                                    @if(filled($path->description))
                                        <p class="text-sm leading-relaxed text-slate-600">{{ $path->description }}</p>
                                    @endif

                                    <dl class="mt-4 space-y-2 text-sm">
                                        <div class="flex justify-between gap-3">
                                            <dt class="text-slate-500">Kuota</dt>
                                            <dd class="font-bold text-slate-900">{{ number_format($path->quota, 0, ',', '.') }} kursi</dd>
                                        </div>
                                        <div class="flex justify-between gap-3">
                                            <dt class="text-slate-500">Sisa Kuota</dt>
                                            <dd class="font-bold {{ $path->remainingQuota() > 0 ? 'text-green-700' : 'text-red-600' }}">
                                                {{ number_format($path->remainingQuota(), 0, ',', '.') }} kursi
                                            </dd>
                                        </div>
                                        <div class="flex justify-between gap-3">
                                            <dt class="text-slate-500">Periode</dt>
                                            <dd class="text-right font-semibold text-slate-900">
                                                {{ tanggal_id($path->opens_at, 'j M Y') }} &ndash; {{ tanggal_id($path->closes_at, 'j M Y') }}
                                            </dd>
                                        </div>
                                    </dl>

                                    @if(filled($path->requirements))
                                        <div x-data="{ open: false }" class="mt-4 border-t border-slate-200 pt-3">
                                            <button type="button" @click="open = ! open"
                                                class="flex w-full items-center justify-between text-xs font-bold uppercase tracking-wide text-primary-700 hover:text-primary-900">
                                                Syarat Pendaftaran
                                                <svg class="size-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                            </button>
                                            <div x-cloak x-show="open" class="prose prose-sm prose-slate mt-2 max-w-none text-sm prose-ul:pl-4">
                                                {!! $path->requirements !!}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-auto pt-5">
                                        @if($open)
                                            <a href="{{ route('ppdb.create', $path->slug) }}"
                                                class="flex w-full items-center justify-center gap-2 rounded-sm bg-primary-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-primary-800">
                                                Daftar Sekarang
                                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                            </a>
                                        @elseif($path->isUpcoming())
                                            <p class="rounded-sm border border-dashed border-sky-300 bg-sky-50 px-4 py-2.5 text-center text-xs font-semibold text-sky-800">
                                                Dibuka {{ tanggal_id($path->opens_at, 'j F Y') }} pukul {{ $path->opens_at->format('H:i') }}
                                            </p>
                                        @else
                                            <p class="rounded-sm border border-dashed border-slate-300 bg-slate-50 px-4 py-2.5 text-center text-xs font-semibold text-slate-500">
                                                Pendaftaran jalur ini telah ditutup
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Alur pendaftaran: 4 langkah --}}
            <div class="mt-12">
                @include('themes.default.partials.section-title', ['title' => 'Alur Pendaftaran'])
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach([
                        ['Pilih Jalur', 'Pelajari syarat dan periode tiap jalur, lalu pilih yang paling sesuai dengan kondisi Anda.', 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z'],
                        ['Isi Formulir', 'Lengkapi data diri, data orang tua, dan asal sekolah, lalu unggah pas foto serta dokumen pendukung.', 'M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897l12.682-12.682Z'],
                        ['Verifikasi Berkas', 'Panitia memeriksa berkas Anda. Simpan nomor pendaftaran untuk memantau perkembangan status.', 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
                        ['Pengumuman', 'Hasil seleksi (diterima/tidak) diumumkan melalui halaman Cek Status menggunakan nomor pendaftaran.', 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73'],
                    ] as $index => [$stepTitle, $stepBody, $iconPath])
                        <div class="relative rounded-sm border border-slate-300 bg-white p-5 shadow-sm">
                            <span class="absolute right-4 top-4 font-heading text-4xl font-bold text-primary-100">{{ $index + 1 }}</span>
                            <span class="flex size-11 items-center justify-center rounded border border-primary-100 bg-primary-50 text-primary-700">
                                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/></svg>
                            </span>
                            <h3 class="mt-3 font-heading text-sm font-bold text-slate-900">{{ $stepTitle }}</h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-slate-500">{{ $stepBody }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FAQ singkat --}}
            <div class="mt-12">
                @include('themes.default.partials.section-title', ['title' => 'Pertanyaan Umum'])
                <div class="space-y-3">
                    @foreach([
                        'Berkas apa saja yang perlu disiapkan?' => 'Pas foto terbaru (JPG/PNG), kartu keluarga, akta kelahiran, dan rapor. Seluruh berkas diunggah dalam format PDF/JPG/PNG dengan ukuran maksimal 2 MB per berkas.',
                        'Apakah satu anak boleh mendaftar di dua jalur?' => 'Tidak. NISN hanya dapat didaftarkan satu kali pada tahun ajaran yang sama. Pilih jalur yang paling sesuai sebelum mengirim formulir.',
                        'Bagaimana cara mengetahui hasil seleksi?' => 'Gunakan menu Cek Status dengan memasukkan nomor pendaftaran dan tanggal lahir. Status akan berubah dari Menunggu Verifikasi menjadi Terverifikasi, lalu Diterima atau Ditolak.',
                        'Nomor pendaftaran saya hilang, bagaimana?' => 'Hubungi panitia PPDB melalui kontak sekolah dengan menyebutkan NISN dan nama lengkap. Panitia akan membantu menelusuri nomor pendaftaran Anda.',
                    ] as $question => $answer)
                        <div x-data="{ open: false }" class="rounded-sm border border-slate-300 bg-white shadow-sm">
                            <button type="button" @click="open = ! open"
                                class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left text-sm font-bold text-slate-900 hover:text-primary-700">
                                {{ $question }}
                                <svg class="size-4 shrink-0 text-primary-600 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-cloak x-show="open" class="border-t border-slate-200 px-5 py-4 text-sm leading-relaxed text-slate-600">
                                {{ $answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
