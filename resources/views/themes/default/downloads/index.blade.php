@extends('themes.default.layouts.app')

@section('title', 'Unduhan')

@push('meta')
    @include('themes.default.partials.meta-og', ['title' => 'Unduhan'])
@endpush

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => 'Unduhan',
        'subtitle' => 'Berkas dan dokumen resmi sekolah yang dapat diunduh.',
        'breadcrumbs' => ['Unduhan' => null],
    ])

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @if($files->isNotEmpty())
            <div x-data="{ active: 'semua' }">
                {{-- Filter kategori --}}
                <div class="mb-8 flex flex-wrap gap-2">
                    <button type="button" @click="active = 'semua'"
                        class="rounded px-4 py-2 text-sm font-semibold transition sm:py-1.5"
                        :class="active === 'semua' ? 'bg-primary-600 text-white' : 'border border-slate-300 bg-white text-slate-600 hover:border-primary-600 hover:text-primary-700'">
                        Semua
                    </button>
                    @foreach($files->keys() as $category)
                        <button type="button" @click="active = @js($category)"
                            class="rounded px-4 py-2 text-sm font-semibold transition sm:py-1.5"
                            :class="active === @js($category) ? 'bg-primary-600 text-white' : 'border border-slate-300 bg-white text-slate-600 hover:border-primary-600 hover:text-primary-700'">
                            {{ $category }}
                        </button>
                    @endforeach
                </div>

                {{-- Tabel berkas per kategori --}}
                @foreach($files as $category => $categoryFiles)
                    <section x-show="active === 'semua' || active === @js($category)" class="mb-10">
                        <h2 class="border-l-4 border-amber-400 pl-3 font-heading text-lg font-bold leading-snug tracking-tight text-slate-800">{{ $category }}</h2>
                        <div class="mt-3 overflow-hidden rounded-sm border border-slate-300 bg-white shadow-sm">
                            <table class="w-full text-sm">
                                <thead class="hidden bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 sm:table-header-group">
                                    <tr class="border-b border-slate-200">
                                        <th class="px-4 py-3">Dokumen</th>
                                        <th class="w-20 px-4 py-3">Jenis</th>
                                        <th class="w-24 px-4 py-3">Ukuran</th>
                                        <th class="w-24 px-4 py-3">Diunduh</th>
                                        <th class="w-28 px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($categoryFiles as $file)
                                        @php($media = $file->getFirstMedia('file'))
                                        @php($ext = strtoupper($media->extension ?? 'FILE'))
                                        @php($extColor = match ($ext) {
                                            'PDF' => 'bg-red-50 text-red-700 border-red-200',
                                            'DOC', 'DOCX' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'XLS', 'XLSX' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'PPT', 'PPTX' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'ZIP', 'RAR' => 'bg-violet-50 text-violet-700 border-violet-200',
                                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                                        })
                                        <tr class="flex flex-col gap-2 px-4 py-4 transition hover:bg-slate-50 sm:table-row sm:p-0">
                                            <td class="sm:px-4 sm:py-3.5">
                                                <div class="flex items-start gap-3">
                                                    <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded border {{ $extColor }}">
                                                        <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                                    </span>
                                                    <div class="min-w-0">
                                                        <p class="font-semibold text-slate-900">{{ $file->title }}</p>
                                                        @if(filled($file->description))
                                                            <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $file->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- Di mobile baris jadi kartu bertumpuk; beri label agar angka tidak ambigu --}}
                                            <td class="text-xs text-slate-500 sm:px-4 sm:py-3.5">
                                                <span class="inline-block rounded border px-1.5 py-0.5 font-semibold {{ $extColor }}">{{ $ext }}</span>
                                            </td>
                                            <td class="text-xs text-slate-500 sm:px-4 sm:py-3.5"><span class="font-semibold text-slate-400 sm:hidden">Ukuran: </span>{{ $media?->human_readable_size ?? '-' }}</td>
                                            <td class="text-xs text-slate-500 sm:px-4 sm:py-3.5"><span class="font-semibold text-slate-400 sm:hidden">Diunduh: </span>{{ number_format($file->download_count, 0, ',', '.') }}x</td>
                                            <td class="sm:px-4 sm:py-3.5 sm:text-right">
                                                <a href="{{ route('downloads.download', $file->slug) }}"
                                                    class="inline-flex items-center gap-1.5 rounded-sm bg-primary-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-primary-800 sm:px-3.5 sm:py-2">
                                                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                                    Unduh
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endforeach
            </div>
        @else
            @include('themes.default.partials.empty-state', [
                'title' => 'Belum ada berkas unduhan yang dipublikasikan.',
                'message' => 'Dokumen resmi sekolah akan tersedia di sini setelah diunggah.',
                'iconPath' => 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3',
                'actionUrl' => route('home'),
                'actionLabel' => 'Kembali ke Beranda',
            ])
        @endif
    </div>
@endsection
