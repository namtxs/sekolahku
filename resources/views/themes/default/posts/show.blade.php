@extends('themes.default.layouts.app')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: ($post->excerpt ?: ($settings->meta_description ?? '')))

@push('meta')
    @include('themes.default.partials.meta-og', [
        'type' => 'article',
        'title' => $post->meta_title ?: $post->title,
        'description' => $post->meta_description ?: ($post->excerpt ?: strip_tags($post->body)),
        'image' => $post->getFirstMediaUrl('featured', 'large') ?: null,
        'url' => route('posts.show', $post->slug),
        'publishedTime' => $post->published_at?->toIso8601String(),
    ])
@endpush

@section('content')
    @include('themes.default.partials.page-header', [
        'title' => $post->title,
        'breadcrumbs' => array_filter([
            'Berita' => route('posts.index'),
            $post->category?->name => $post->category ? route('categories.show', $post->category->slug) : null,
        ]) + [$post->title => null],
    ])

    @php($shareUrl = route('posts.show', $post->slug))

    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="lg:col-span-2">
            <article class="rounded-sm border border-slate-300 bg-white shadow-sm">
                <div class="p-5 sm:p-8">
                    {{-- Meta artikel --}}
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-slate-200 pb-4 text-sm text-slate-500">
                        <time datetime="{{ $post->published_at?->toDateString() }}" class="inline-flex items-center gap-1.5">
                            <svg class="size-4 text-primary-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/></svg>
                            {{ tanggal_id($post->published_at) }}
                        </time>
                        @if($post->user)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="size-4 text-primary-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 9a7 7 0 1 1 14 0H3Z" clip-rule="evenodd"/></svg>
                                {{ $post->user->name }}
                            </span>
                        @endif
                        @if($post->category)
                            <a href="{{ route('categories.show', $post->category->slug) }}"
                                class="inline-flex items-center gap-1.5 font-semibold text-primary-700 hover:text-primary-900">
                                <svg class="size-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 0 0 3 5.5v2.879a2.5 2.5 0 0 0 .732 1.767l6.5 6.5a2.5 2.5 0 0 0 3.536 0l2.878-2.878a2.5 2.5 0 0 0 0-3.536l-6.5-6.5A2.5 2.5 0 0 0 8.38 3H5.5ZM6 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                                {{ $post->category->name }}
                            </a>
                        @endif
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="size-4 text-primary-700" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
                            Dilihat {{ number_format($post->views, 0, ',', '.') }} kali
                        </span>
                    </div>

                    @php($image = $post->getFirstMediaUrl('featured', 'large'))
                    @if($image)
                        <figure class="mt-5">
                            {{-- Rasio tetap mencegah layout shift saat gambar dimuat --}}
                            <img src="{{ $image }}" alt="{{ $post->title }}" class="aspect-video max-h-[480px] w-full rounded object-cover">
                        </figure>
                    @endif

                    {{-- Isi artikel --}}
                    <div class="prose prose-slate prose-article mt-6 max-w-none prose-headings:font-heading prose-headings:tracking-tight">
                        {!! $post->body !!}
                    </div>

                    {{-- Tags --}}
                    @if($post->tags->isNotEmpty())
                        <div class="mt-8 flex flex-wrap items-center gap-2 border-t border-slate-200 pt-5">
                            <span class="text-sm font-semibold text-slate-500">Tag:</span>
                            @foreach($post->tags as $tag)
                                <a href="{{ route('tags.show', $tag->slug) }}"
                                    class="rounded border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600 transition hover:border-primary-600 hover:bg-primary-50 hover:text-primary-800">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Bagikan --}}
                    <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-200 pt-5">
                        <span class="text-sm font-semibold text-slate-500">Bagikan:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-1.5 rounded bg-[#1877f2] px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></svg>
                            Facebook
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($post->title.' '.$shareUrl) }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-1.5 rounded bg-[#25d366] px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                            WhatsApp
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-1.5 rounded bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90">
                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644Z"/></svg>
                            X
                        </a>
                    </div>
                </div>
            </article>

            {{-- Berita terkait --}}
            @if($related->isNotEmpty())
                <section class="mt-10">
                    @include('themes.default.partials.section-title', ['title' => 'Berita Terkait'])
                    <div class="rounded-sm border border-slate-300 bg-white p-5 shadow-sm">
                        @foreach($related as $relatedPost)
                            @include('themes.default.partials.post-row', ['post' => $relatedPost])
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Komentar (partial reusable, juga dipakai halaman statis) --}}
            @include('themes.default.partials.comments', ['post' => $post, 'comments' => $comments])
        </div>

        {{-- Sidebar: di mobile turun ke bawah konten dengan garis pemisah --}}
        <div class="border-t border-slate-300 pt-8 lg:border-t-0 lg:pt-0">
            @include('themes.default.partials.sidebar')
        </div>
    </div>
@endsection
